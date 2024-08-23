<?php

namespace Modules\Admin\Repositories\Implementations;

use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use App\Models\User;
use Illuminate\Support\Str; 
use Modules\Admin\Entities\Sites; 
use Modules\Admin\Repositories\Interfaces\SitesInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;

class SitesRepository implements SitesInterface
{

    /**
     * Get new instance
     */

    public function ajaxgetlist()
    {
        if (request()->ajax()) { 
            $data = Sites::select('sites.*') 
                ->orderBy('sites.status', 'desc')
                ->orderBy('sites.created_at', 'desc')->get();
            return datatables()->of($data)
                ->setRowClass(function ($request) {
                    return $request->status == 1 ? 'nk-tb-item' : 'nk-tb-item font-italic text-muted';
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('d/m/Y H:i:s');
                    // return [
                    //     'display'   => $request->created_at->format('d/m/Y'),
                    //     'timestamp' => $request->created_at,
                    // ];
                })
                ->editColumn('site_name', function ($request) {
                   return $request->site_name;
                })
                 
                ->editColumn('site_code', function ($request) {
                    return $request->site_code;
                 })
                  
                ->editColumn('status', function ($request) {
                    if ($request->status == 1) {
                        return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Active</label>';
                   } else {
                       return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Inactive</label>';
                   }
                })
                 
                ->addColumn('action', function ($data) {
                    // return '<a class="btn btn-success btn-sm" href="'.url('admin/sites/edit',$data->id).'">Edit</a>';
                    return $link =  '<div class="demo-inline-spacing">
                    <a href="' . url('admin/sites/edit', encrypt_decrypt('encrypt',$data->id)) . '" ><button type="button" class="btn btn-icon btn-primary">
                    <span class="tf-icons bx bx-pencil bx-22px"></span>
                    </button></a>
                    
                
                </div>';
                })
                ->rawColumns(['created_at', 'site_name', 'site_domain', 'oboarding_fee', 'status','commericals','action'])
                ->make(true);
        }
    }

    public function siteslist()
    {

        $sites = Sites::pluck('site_name', 'id')->all();
        return $sites;
    }

    public function save(Sites $sites, array $data)
    {
        DB::beginTransaction();
        try {
            $sites        = Sites::create($data);
            DB::commit();
            return $sites;
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
        }
    }

    public function update(Sites $sites, array $data)
    {
        if (isset($data['sites_logo_image'])) {
            $file                     = $data['sites_logo_image'];
            $name                     = time() . $file->getClientOriginalName();
            $s3filepath               = BasicHelper::uploadFileToDisk('s3', $file, $name, $mimeType = '');
            $data['sites_logo_image'] = $s3filepath;
        }

        if (isset($data['sites_favicon'])) {
            $file                  = $data['sites_favicon'];
            $name                  = time() . $file->getClientOriginalName();
            $s3filepath            = BasicHelper::uploadFileToDisk('s3', $file, $name, $mimeType = '');
            $data['sites_favicon'] = $s3filepath;
        }

        if (isset($data['site_agreement'])) {
            $file                   = $data['site_agreement'];
            $name                   = time() . $file->getClientOriginalName();
            $s3filepath             = BasicHelper::uploadFileToDisk('s3', $file, $name, $mimeType = '');
            $data['site_agreement'] = $s3filepath;
        }

        if (isset($data['terms'])) {
            $file          = $data['terms'];
            $name          = time() . $file->getClientOriginalName();
            $s3filepath    = BasicHelper::uploadFileToDisk('s3', $file, $name, $mimeType = '');
            $data['terms'] = $s3filepath;
        }

        $data['email_service'] = isset($data['email_service']) ? 1 : 0;
        $data['secure_transaction'] = isset($data['secure_transaction']) ? 1 : 0;
        if (isset($data['bank_service']) && ($data['bank_service'] == 1 || $data['bank_service'] == 2)) {
            if ($data['bank_service'] == 1) {
                $data['bank_service'] = 1;
                $data['multi_bank_value'] = 0;
            } elseif ($data['bank_service'] == 2) {
                $data['bank_service'] = 2;
                if ($data['multi_bank_value'] > 5 || $data['multi_bank_value'] < 1) {
                    $data['multi_bank_value'] = 2;
                }
            }
        } else {
            $data['bank_service'] = 0;
            $data['multi_bank_value'] = 0;
        }

        if (!isset($data['multi_bank_value']) || $data['bank_service'] == 1) {
            $data['multi_bank_value'] = 0;
        }
        $sites->update($data);
        $sites->associateSiteGateway()->sync(array($data['pay_in'], $data['pay_out']));
        if (!empty($data['apis'])) {
            $sites->associateapis()->sync($data['apis']);
        }

        $checkSiteMeta = $this->getSiteConfig($sites->id);
        if(isset($checkSiteMeta)){
            $favicon_type = isset($data['sites_favicon_type']) ? $data['sites_favicon_type'] : $checkSiteMeta->sites_favicon_type;
            $favicon = isset($data['sites_favicon']) ? $data['sites_favicon'] : $checkSiteMeta->sites_favicon;
            $logo = isset($data['sites_logo_image']) ? $data['sites_logo_image'] : $checkSiteMeta->sites_logo_image;
            $logo_size = isset($data['sites_logo_size']) ? $data['sites_logo_size'] : $checkSiteMeta->sites_logo_size;
            $powered = isset($data['powered']) ? $data['powered'] : $checkSiteMeta->powered;
            $powered_url = isset($data['powered_url']) ? $data['powered_url'] : $checkSiteMeta->powered_url;
            $bg_color = isset($data['sites_sidebar_bg_color_code']) ? $data['sites_sidebar_bg_color_code'] : $checkSiteMeta->sites_sidebar_bg_color_code;
            $menu_text = isset($data['sites_sidebar_menu_text_color_code']) ? $data['sites_sidebar_menu_text_color_code'] : $checkSiteMeta->sites_sidebar_menu_text_color_code;
            $menu_text_hover = isset($data['sites_sidebar_menu_text_hover_color_code']) ? $data['sites_sidebar_menu_text_hover_color_code'] : $checkSiteMeta->sites_sidebar_menu_text_hover_color_code;
            $sub_menu_bg = isset($data['sites_sidebar_sub_menu_bg_color_code']) ? $data['sites_sidebar_sub_menu_bg_color_code'] : $checkSiteMeta->sites_sidebar_sub_menu_bg_color_code;
            $sub_menu_text_color = isset($data['sites_sidebar_sub_menu_txt_color_code']) ? $data['sites_sidebar_sub_menu_txt_color_code'] : $checkSiteMeta->sites_sidebar_sub_menu_txt_color_code;
            $sub_menu_text_hover = isset($data['sites_sidebar_sub_menu_txt_hover_color_code']) ? $data['sites_sidebar_sub_menu_txt_hover_color_code'] : $checkSiteMeta->sites_sidebar_sub_menu_txt_hover_color_code;
            $btn_bg = isset($data['sites_btn_bg_color_code']) ? $data['sites_btn_bg_color_code'] : $checkSiteMeta->sites_btn_bg_color_code;
            $btn_text_color = isset($data['sites_btn_txt_color_code']) ? $data['sites_btn_txt_color_code'] : $checkSiteMeta->sites_btn_txt_color_code;
            $temp_type = isset($data['temp_type']) ? $data['temp_type'] : $checkSiteMeta->temp_type;
        }

        $metaData = array('sites_id' => $sites->id,'sites_logo_image' => $logo,'sites_favicon' => $favicon,'sites_logo_size' => $logo_size,'sites_favicon_type' => $favicon_type,'powered' => $powered,'powered_url' => $powered_url,'sites_sidebar_bg_color_code' => $bg_color,'sites_sidebar_menu_text_color_code' => $menu_text,'sites_sidebar_menu_text_hover_color_code' => $menu_text_hover,'sites_sidebar_sub_menu_bg_color_code' => $sub_menu_bg,'sites_sidebar_sub_menu_txt_color_code' => $sub_menu_text_color,'sites_sidebar_sub_menu_txt_hover_color_code' => $sub_menu_text_hover,'sites_btn_bg_color_code'=> $btn_bg,'sites_btn_txt_color_code'=> $btn_text_color,'temp_type'=>$temp_type,'updated_at'=>Carbon::now());

        $this->insertOrUpdateSiteMeta($metaData);

        DB::table('site_notifications')->where('site_id', $sites->id)->update(['status' => 0, 'updated_at' => Carbon::now()]);
        //if data is empty in ipdomain variable
        if(!empty($data['ip_domain'][0])){
            DB::table('domain_ip_whitelist')->where('site_id', $sites->id)->delete();
            foreach ($data['ip_domain'] as $key => $val) {
                    DB::table('domain_ip_whitelist')->insertGetId([
                        'site_id'     => $sites->id,
                        'type'  => $data['whitelist_type'][$key],
                        'value' => $data['ip_domain'][$key],
                        'status'      => 1,
                        'created_at'  => Carbon::now(),
                    ]);
            }
        }

        if (isset($data['templates'])) {
            foreach ($data['templates'] as $key => $templateValue) {
                $checkSiteTemplates = DB::table('site_notifications')->where('site_id', $sites->id)->where('template_id', $templateValue)->first('status');

                if (isset($checkSiteTemplates)) {
                    // print_r('inside');
                    if ($checkSiteTemplates->status == 0) {
                        DB::table('site_notifications')->where('site_id', $sites->id)->where('template_id', $templateValue)->update(['status' => 1, 'updated_at' => Carbon::now()]);
                    }

                } else {
                    DB::table('site_notifications')->insertGetId([
                        'site_id'     => $sites->id,
                        'alert_type'  => 'email',
                        'template_id' => $templateValue,
                        'status'      => 1,
                        'created_at'  => Carbon::now(),
                    ]);
                }
            }
        }

        return $sites;
    }

    public function firstornew($id)
    {
        if (!empty($id)) {

            return Sites::where('id', $id)->first();
        }
        return new Sites();
    }

    public function siteBydomain($domain)
    {
        if (!empty($domain)) {

            return Sites::where('site_domain', $domain)->first();
        }
        return new Sites();
    }

    public function sitesRoles()
    {

        //$sites = Sites::select('id','site_name')->whereIn('sites.id',get_current_loggedin_user_siteid('0'))->with('associateroles')->has('associateroles')->get();

        $siteids = Auth::user()->roles->pluck('id')->toArray();

        //$sites = DB::table('roles')->select('*')->leftJoin('role_has_roles','roles.id','role_has_roles.child_role_id')->whereIn('parent_role_id',$siteids)->where('roles.id','role_has_roles.child_role_id')->get();

        /*  $sites = Sites::select('sites.id','sites.site_name')
        ->leftJoin('roles','sites.id','roles.site_id')->get();*/

        $user     = \Auth::user();
        $userRole = $user->roles->first();
        if ($userRole->id == 1) {
            $sites = DB::table('roles')->select('roles.id', 'roles.name', 'sites.site_name')->leftJoin('sites', 'roles.site_id', 'sites.id')->get();
        } else {
            $sites = DB::table('roles')->select('roles.id', 'roles.name', 'sites.site_name')->leftJoin('sites', 'roles.site_id', 'sites.id')->whereIn('roles.id', function ($query) use ($siteids) {
                $query->select('role_id')->from('role_manager')->whereIn('parent_id', $siteids);
            })->get();
        }

        return $sites;
    }

    public function getpayinout($type)
    {
        if (isset($type)) {
            return DB::table('payment_gateways')->where('type', $type)->pluck('name', 'id');
        } else {
            return DB::table('payment_gateways')->pluck('name', 'id');
        }
    }

    public function getrolesbysite($site_id)
    {
        $user     = \Auth::user();
        $userRole = $user->roles->first();
        if ($userRole->id == 1) {
            if (isset($site_id)) {
                $sitesList = Sites::with('associateroles')->has('associateroles')->select('id', 'site_name')->where('id', $site_id)->orderBy('site_name')->get();
            } else {
                $sitesList = Sites::with('associateroles')->has('associateroles')->select('id', 'site_name')->orderBy('site_name')->get();
            }
        } else {
           $sitesList = Sites::with('associateroles')->has('associateroles')->select('id', 'site_name')->where('id',epcache('site_id'))->orderBy('site_name')->get();
        }
        return $sitesList;
    }

    public function ajaxgetsitesinvoices()
    {
        $siteCode = epcache('site_code');
        if (request()->ajax()) {
            $query = DB::table('sites_invoices as si')->leftjoin('sites as s','s.id','=','si.site_id')->select('s.site_name','si.created_at','si.inv_month','si.inv_year','si.amount','si.amount_excl_gst','si.amount_tds','si.invoice_no','si.invoice_path');
            if ($siteCode!='TRUST') {
                $query->where('si.site_id',epcache('site_id'));
            }
            $data = $query->get();
            return datatables()->of($data)
                ->setRowClass('nk-tb-item')
                ->editColumn('created_at', function ($request) {
                    return [
                        'display'   => date('d-m-Y', strtotime($request->created_at)),
                        'timestamp' => $request->created_at,
                    ];
                })
                ->editColumn('invoice_date', function ($request) {
                    return date("M", mktime(0, 0, 0, $request->inv_month, 10)).'-'.$request->inv_year;
                })
                ->editColumn('site_name', function ($request) {
                    return '<span class="site_name">' . $request->site_name . '</span>';
                })
                ->editColumn('amount', function ($request) {
                    return '<span class="amount">' . $request->amount . '</span>';
                })
                ->editColumn('invoice', function ($request) {
                    return '<a target="_blank" href="'.$request->invoice_path.'" class="btn btn-trigger btn-icon" title="Invoice"><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size: 16px;"></i></a>';
                })
                ->rawColumns(['created_at', 'site_name', 'invoice_date', 'amount', 'invoice'])
                ->make(true);
        }
    }

    public function monthly_invoice_generate($data)
    {
        \DB::beginTransaction();
    try {
            $getSite = Sites::where('id', $data['site_id'])->first();
            if ($getSite == null) {
                throw new \Exception('There is no site in our record.');
            }
            $escrow = Escrow::where('user_id',$getSite->id)->where('ac_type',-2)->first();
            if ($escrow == null) {
                throw new \Exception('There is no account for this site.');
            }
            $trustId = DB::table('virtual_escrow_accounts as a')->leftjoin('sites as s','s.id','=','a.user_id')->select('a.id')->where('a.ac_type',-1)->where('s.site_code','TRUST')->first();
            $txn = DB::select(DB::raw('select sum(amount) as amount from virtual_escrow_txns where account_id='.$escrow->id.' and party_ac_id='.$trustId->id.' and type="debit" and month(created_at)='.$data['inv_month'].' and year(created_at)='.$data['inv_year'].''));
            $ep_fee = $txn[0]->amount;
            if (isset($ep_fee)) {
                $monthName = date("M", mktime(0, 0, 0, $data['inv_month'], 10));
                $particular = "Escrow Fee for ".$getSite->site_name." for the Month of ".$monthName."-".$data['inv_year'];
                \Log::info($_SERVER['HTTP_HOST']." : Generating Monthly Invoice Pdf for epfee =".$ep_fee.", particular=".$particular);
                $envData = env('APP_ENV', 'local');
                $domain = $envData == 'prod' ? 'Central' : 'Centraluat';
                $filename = $getSite->invoice_code.'_INVOICE_'.sprintf("%02d", $data['inv_month']).$data['inv_year'].'_'.time().'.pdf';
                $filePath = $domain.'-documents/' . $filename;
                $s3filepathagencyletter = 'https://d1tq5769y0bfry.cloudfront.net/'.$domain.'-documents/' . $filename;

                $getInvoice = DB::table('sites_invoices')->where('site_id', $getSite->id)->where('inv_month', $data['inv_month'])->where('inv_year', substr($data['inv_year'],2))->first();
                if (isset($getInvoice)) {
                    throw new \Exception('Invoice for this month already exists!');
                } else {
                    $lastID = DB::table('sites_invoices')->insertGetId([
                    'site_id' => $getSite->id,
                    'inv_month' => $data['inv_month'],
                    'inv_year' => substr($data['inv_year'],2),
                    'amount' => $ep_fee,
                    'invoice_no' => 1,
                    'invoice_path' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $msg = 'Invoice has been generated successfully for this month.';
                }

                $site_address = get_user_info_by_roles($getSite->id);

                if (empty($site_address->gstin_number)) {
                    throw new \Exception('GSTN number is missing');
                } elseif (empty($site_address->address_line_one) && empty($site_address->address_line_two) && empty($site_address->postal_code)) {
                    throw new \Exception('Address is missing');
                } elseif (empty($site_address->state_name) && empty($site_address->city_name)) {
                    throw new \Exception('State Or City is missing');
                } elseif (empty($site_address) && empty($getSite->invoice_code)) {
                    throw new \Exception('Invoice Code is missing');
                }

                $invoice_no = 'TTPL/'.$getSite->invoice_code.'/'.strtoupper($monthName).$data['inv_year'];

                $data = array(
                    'i2cFees' => round($ep_fee/1.18, 2),
                    'particular' => $particular,
                    'invoice_no' => $invoice_no,
                    'txndate' => date('d-F-Y'),
                    'site_name' => $getSite->site_name,
                    'site_pan' => $getSite->site_pan,
                    'email'=> get_first_site_admins_email($getSite->id),
                    'gstin_number'=>$site_address->gstin_number,
                    'address_line_one'=>$site_address->address_line_one,
                    'address_line_two'=>$site_address->address_line_two,
                    'postal_code'=>$site_address->postal_code,
                    'state_name'=>$site_address->state_name,
                    'city_name'=>$site_address->city_name,
                );

                $pdf = \PDF::loadView('admin::invoices.site_invoice_pdf',['info'=>$data]);
                Storage::disk('s3')->put($filePath, $pdf->output());
                $percent=18; //GST percent
                $base_amt = $ep_fee*(100/(100+$percent));
                $calTdsAmount = $base_amt * (10/100);
                $other_particular = 'TDS Refund for Invoice No #'.$invoice_no;

                $trust_acc = DB::table('sites')->join('virtual_escrow_accounts as vea','sites.id','vea.user_id')->select('vea.id')->where('sites.site_code','trust')->where('vea.ac_type',-1)->first();

                BasicHelper::escrow_account_transaction(1, 'debit', 0, $other_particular, $calTdsAmount, $trust_acc->id, $escrow->id, null, 1, null);
                BasicHelper::escrow_account_transaction($getSite->id, 'credit', 0, $other_particular, $calTdsAmount, $escrow->id, $trust_acc->id, null, 1, null);

                DB::table('sites_invoices')->where('id',$lastID)->update(array('invoice_no'=>$data['invoice_no'],'invoice_path'=>$s3filepathagencyletter,'amount'=>$ep_fee,'amount_excl_gst'=>$base_amt,'amount_tds'=>$calTdsAmount,'updated_at' => date('Y-m-d H:i:s')));
                \Log::info($_SERVER['HTTP_HOST']." : Monthly Invoice Generated with epfee =".$ep_fee.", particular=".$particular.", message=".$msg);
                try {
                    $mailData = array(
                        'site_name' => $getSite->site_name,
                        'invoiceno' => $data['invoice_no'],
                        'particular' => $particular,
                        'amount' => numberToINR($ep_fee,1,1),
                        'files' => $s3filepathagencyletter
                    );
                    \Log::info($_SERVER['HTTP_HOST']." : Invoice Mail Data =".json_encode($mailData));
                    BasicHelper::send_email($getSite->id, 'generate_sites_invoices', $mailData, config('mail.ADMINS'), config('mail.ADMINS'), config('mail.ADMINS'));
                    //dispatch(new SendInvoiceJob($mailData));
                } catch (\Exception $exe) {
                    \Log::error($_SERVER['HTTP_HOST']." : Error in sending invoice: ".$exe->getMessage());
                }
            } else {
                throw new \Exception('There is no transaction of this month and year');
            }
        } catch (\Exception $ex){
            \Log::error($_SERVER['HTTP_HOST']." : Error in monthly_invoice_generate(): ".$ex->getMessage());
            \DB::rollBack();
            return array('success' => 0,'message' => $ex->getMessage());
        }
        \DB::commit();
            return array('success' => 1,'message' => $msg);
    }

    public function getSiteConfig($id)
    {
        $data = DB::table('sites_meta')->where('sites_id',$id)->first();
        return $data;
    }

    public function insertOrUpdateSiteMeta($data)
    {
        $siteIdCheck = $this->getSiteConfig($data['sites_id']);
        if(isset($siteIdCheck)){
            DB::table('sites_meta')->where('sites_id',$data['sites_id'])->update($data);
        }else{
            DB::table('sites_meta')->insert($data);
        }
    }

    public function getColorThemeById($id)
    {
        $data = DB::table('color_themes')->where('id',$id)->first();
        if(isset($data)){
            return $data;
        }
    }

    public function getColorThemes()
    {
        $data = DB::table('color_themes')->where('status',1)->get();
        return $data;
    }

    public function getSiteInvoices($id)
    {
        $data = DB::table('sites_invoices')->select('inv_month','inv_year','amount','invoice_no','invoice_path','created_at')->where('site_id',$id)->get();
        return datatables()->of($data)
        ->setRowClass('nk-tb-item')
        ->editColumn('created_at', function ($request) {
            return [
                'display'   => date('d-m-Y', strtotime($request->created_at)),
                'timestamp' => $request->created_at,
            ];
        })
        ->editColumn('invoice_date', function ($request) {
            return date("M", mktime(0, 0, 0, $request->inv_month, 10)).'-'.$request->inv_year;
        })

        ->editColumn('amount', function ($request) {
            return '<span class="amount">' . $request->amount . '</span>';
        })
        ->editColumn('invoice_path', function ($request) {
            return '<a target="_blank" href="'.$request->invoice_path.'" class="btn btn-trigger btn-icon" title="Invoice"><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size: 16px;"></i></a>';
        })
        ->rawColumns(['created_at', 'invoice_date', 'amount', 'invoice_path'])
        ->make(true);
    }

    public function getSiteAgreements($id)
    {
        $data = DB::table('agreements')->leftjoin('sites','sites.agr_id','agreements.id')->select('agreements.created_at','agreements.url as doc_path','sites.onboard_nda as nda_status')->where('sites.id',$id)->get();

        return datatables()->of($data)
        ->setRowClass('nk-tb-item')
        ->editColumn('created_at', function ($request) {
            return [
                'display'   => date('d-m-Y', strtotime($request->created_at)),
                'timestamp' => $request->created_at,
            ];
        })
        ->editColumn('doc_path', function($request) {
            if($request->doc_path != ''){
                return '<a href="' . $request->doc_path . '" target="_blank">Agreement <i class="icon ni ni-file-text" aria-hidden="true"></i></a>';
            } else {
                return '-';
            }
        })
        ->editColumn('nda_status',function($request){
            switch ($request->nda_status) {

                case "1":
                return '<span class="badge badge-outline-info">Agreement Uploaded</span>';
                break;
                case "2":
                return '<span class="badge badge-outline-success">Agreement Signed</span>';
                break;
                case "3":
                    return '<span class="badge badge-outline-primary">Fee Paid</span>';
                default:
                return '-';
             }
        })

        ->rawColumns(['created_at', 'doc_path','nda_status'])
        ->make(true);
    }

    public function ajaxgetsitedeals($id)
    {
        $data = Sites::leftjoin('deals','deals.site_id','sites.id')->select('deals.id as deal_id','deals.deal_ref_id','deals.status','deals.created_at','sites.site_group')->where('sites.id',$id)->orderBy('deals.id', 'DESC')->get();

        return datatables()->of($data)
        ->setRowClass('nk-tb-item')
        ->editColumn('created_at', function ($request) {
            return $request->created_at;
        })
        ->editColumn('deal_id', function($request) use ($id) {
            if($request->status == 0){
                return $request->deal_id;
            }else{
                return '<a class="" href="' . url('admin/deal/show', encrypt_decrypt('encrypt', $request->deal_id)) .'/?site_id=' . $id .'&site_group=' . $request->site_group .'">' . $request->deal_id . '</a>';
            }
        })
        ->editColumn('deal_ref_id', function($request) {
            if($request->deal_ref_id != ''){
                return $request->deal_ref_id;
            } else {
                return '-';
            }
        })
        ->editColumn('status',function($request){
            switch ($request->status) {
                case "1":
                return '<span class="badge badge-outline-success">Active</span>';
                break;
                case "0":
                return '<span class="badge badge-outline-danger">In Active</span>';
                break;
                default:
                return '-';
             }
        })

        ->rawColumns(['created_at', 'deal_id','deal_ref_id','status'])
        ->make(true);
    }

    public function getSiteVirtualAccInfo($id)
    {
        $info = Escrow::select('escrow_ac_no','ifsc_code','bank_name')->where('ac_type',-1)->where('site_id',$id)->first();

        return $info;
    }

    public function getGatewayCode($id)
    {
        $gateway = DB::table('payment_gateways')->select('gateway_code')->where('id',$id)->first();
        return $gateway->gateway_code;
    }
} //end
