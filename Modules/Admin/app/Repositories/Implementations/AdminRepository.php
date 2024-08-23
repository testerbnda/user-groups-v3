<?php

namespace Modules\Admin\Repositories\Implementations;

use Modules\Admin\Repositories\Interfaces\AdminInterface;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Core\Enums\ContentType;
use Modules\Core\Entities\ObjectMetaTag;
use Modules\Core\Enums\Flag;
use DB;
use AUth;
use PDF;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRepository implements AdminInterface
{

    /**
     * Get new instance
     */
    public function storeTemplate($template_name,$template_code,$template_format,$template_type,$temp_level,$site_group,$site_id)
    {
       //return $data = User::orderBy('id','DESC')->paginate(5);
        $data = array('temp_level'=>$temp_level,'user_id'=>Auth::id(),'template_name'=>$template_name,'template_code'=>$template_code,'template_format'=>$template_format,'template_type'=>$template_type,'site_group'=>$site_group,'site_id'=>$site_id,'created_at'=>date('Y-m-d H:i:s'));
        DB::table('templates')->insert($data);
    }

    public function listTemplate()
    {
             return view('admin::template.list');

    }

    public function firstornewtemplate($id){
      if(!empty($id)){
       return DB::table('templates')->where('id',$id)->first();
      }
      return DB::table('templates')->first();
    }

    public function update_template($template, $data)
    {
        return DB::table('templates')->where('id',$template->id)->update($data);
    }

    public function ajaxgettemplist()
    {
        if(request()->ajax()) {
            $data = DB::table('templates')->select('templates.created_at','templates.template_name','templates.template_type','templates.id','sites.site_name','templates.site_group','templates.temp_level','templates.template_code')->leftJoin('sites','templates.site_id','sites.id')->orderBy('created_at','desc')->get();
            return datatables()->of($data)
            ->setRowClass('nk-tb-item')
            ->editColumn('created_at', function ($request) {
                return [
                        'display' =>date('d/m/Y',strtotime($request->created_at)),
                        'timestamp' =>$request->created_at
                    ];
            })
            ->addColumn('template_name', function ($request) {
                return $request->template_name;
            })
            ->editColumn('temp_level', function ($request) {
                switch ($request->temp_level) {
                    case "1":
                        return 'Site Group';
                        break;
                    case "2":
                        return 'Site';
                        break;
                    default:
                        return 'Default';
                }
            })
            ->editColumn('site_name', function ($request) {
                return ($request->site_name != '')?$request->site_name:'-';
            })
            ->editColumn('site_group', function ($request) {
                return ($request->site_group != '')?$request->site_group:'-';
            })
            ->editColumn('template_type', function ($request) {
                switch ($request->template_type) {
                    case "1":
                        return 'Agreement';
                        break;
                    case "2":
                        return 'Email';
                        break;
                    case "3":
                            return 'Onboarding';
                            break;    
                    default:
                        return '-';
                }
            })
            ->addColumn('action', function($data) {
                return '<a class="btn btn-success btn-sm" href="'.url('admin/template/edit',$data->id).'">Edit</a>';
            })
            ->rawColumns(['created_at','template_name','action'])
            ->make(true);
        }
    }

    public function storeAgreement($buyer_name,$seller_name)
    {

        $getRow = DB::table('templates')->select('id','template_format')->where('id',1)->first();
        $string = $getRow->template_format;
        // Search words
        $search = array("{_BUYER_NAME_}", "{_SELLER_NAME_}", "{_LOCATION_}", "{_DATE_}");

        $newArray = array();
        foreach( $search as $bad ){
            if( stristr($string, $bad) !== FALSE )
            {
                $newArray[] = $bad;
            }
        }
        //return $newArray;
        // Replace words
        $get_buyer_name = DB::table('users')->select('name')->where('id',$buyer_name)->first();
        $get_seller_name = DB::table('users')->select('name')->where('id',$seller_name)->first();

        $replace = array();
        if (in_array('{_BUYER_NAME_}', $newArray)) {
            if ($get_buyer_name) {
                $replace[] = $get_buyer_name->name;
            } else {
                $replace[] = '{_BUYER_NAME_}';
            }
        }
        if (in_array('{_SELLER_NAME_}', $newArray)) {
            if ($get_seller_name) {
                $replace[] = $get_seller_name->name;
            } else {
                $replace[] = '{_SELLER_NAME_}';
            }
        }
        if (in_array('{_LOCATION_}', $newArray)) {
            $replace[] = 'Haryana';
        }
        if (in_array('{_DATE_}', $newArray)) {
            $replace[] = date('Y-m-d');
        }
        //return $replace;

        //$replace = array($get_buyer_name->name, $get_seller_name->name);
        $buyerArray = array($buyer_name);
        $sellerArray = array($seller_name);
        $arrayMerge = array_filter(array_merge($buyerArray,$sellerArray));
            // Replace multiple string
        $html = str_replace($newArray, $replace, $string);

        $path = public_path('img');
        $name=time().'_EPEH_'.date('M').'_'.date('y');
        $filePath = 'centralservices-documents/' . $name;
        $s3filepath='https://d1tq5769y0bfry.cloudfront.net/centralservices-documents/'.$name;
        $pdf = PDF::loadHTML($html);
        $pdf->save($path . '/' . $name);
        Storage::disk('s3')->put($filePath, file_get_contents('/home/manish/officeproject/escrowpay-centralservices/public/img/'.$name));

        $data = array('site_id'=>101,'user_id'=>Auth::id(),'url'=>$s3filepath,'created_at'=>date('Y-m-d H:i:s'));
        $lastInserId = DB::table('agreements')->insertGetId($data);
        foreach ($arrayMerge as $key => $value) {
            $data1 = array('agreement_id'=>$lastInserId,'signer_id'=>$value,'etype'=>'agreement','created_at'=>date('Y-m-d H:i:s'));
            DB::table('agreement_signings')->insert($data1);
        }
        return $lastInserId;
    }


    public function storeEnach($request)
    {
        //return $request;
        //return $data = User::orderBy('id','DESC')->paginate(5);
        $data = array('site_id'=>101,'user_id'=>$request->userID,'enter_id'=>$request->enter_id,'account_number'=>$request->account_number,'frequency'=>$request->frequency,'debit_start_date'=>$request->debit_start_date,'debit_end_date'=>$request->debit_end_date,'debit_amount'=>$request->debit_amount,'max_amount'=>$request->max_amount,'uploaded_by'=>Auth::id(),'created_at'=>date('Y-m-d H:i:s'));
        DB::table('enachs')->insert($data);
    }

    public function getEmailTemplates()
    {
        return DB::table('templates')->select('templates.created_at','templates.template_name','templates.template_type','templates.id','sites.site_name','templates.site_group','templates.temp_level','templates.template_code')->leftJoin('sites','templates.site_id','sites.id')->orderBy('template_code','asc')->get();
    }
} //end
