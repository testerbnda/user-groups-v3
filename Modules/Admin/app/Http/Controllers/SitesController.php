<?php


namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Services\SitesService; 
use Modules\Admin\Http\Requests\SitesCreateService;
use Modules\Admin\Http\Requests\SitesUpdateService;
use Modules\Admin\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Sites;
use Carbon\Carbon;
use Auth;
use DB;

class SitesController extends Controller
{
    private $sitesService; 
    private $userService; 

    public function __construct(SitesService $sitesService,UserService $userService)
    {
        $this->sitesService = $sitesService; 
        $this->userService   = $userService; 

    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return view('admin::sites.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $payin  = $this->sitesService->getpayinout(0);
        $payinArray = json_decode(json_encode($payin), true);
        asort($payinArray);

        $payout  = $this->sitesService->getpayinout(1);
        $payoutArray = json_decode(json_encode($payout), true);
        asort($payoutArray);
        return view('admin::sites.create',compact('payinArray','payoutArray'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(SitesCreateService $request)
    {
        try{
            $data = $request->only('site_name','site_code','status');
            if (!preg_match('/^[\w\.]+$/', $data['site_code'])) {
                \Log::info($request->getHttpHost().": Site Code is invalid");
                return redirect()->back()->with(['alert-type' => 'error','message' => 'Site Code is invalid']);
            }
            \Log::info($request->getHttpHost().": Create site request received for site_name =".$data['site_name'].", site_code =".$data['site_code']);
            $sites  = $this->sitesService->firstornew();
            $result = $this->sitesService->save($sites,$data);
            if($result){
                $notification = array(
                    'message' => 'You have successfully added site!',
                    'alert-type' => 'success'
                );
                \Log::info($request->getHttpHost().": Site Created with site_id=".$result->id);
                return redirect()->route('sites.list')->with($notification);
            } else {
                return redirect()->back()->withInput();
            }

        } catch (\Exception $ex) {
            \Log::error($request->getHttpHost().": Create site request failed with Error=".$ex->getMessage());
        }


    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function edit($id)
    { 
        $id     =   encrypt_decrypt('decrypt',$id);
        $site = $this->sitesService->firstornew($id); 

        $site_name = $site->site_name;

        return view('admin::sites.edit',compact('site','site_name'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(SitesUpdateService $request, $id)
    {
        try{
            $data = $request->only('site_name','status');
            \Log::info($request->getHttpHost().":Update site request received for site_id=".$id);
            $sites = $this->sitesService->firstornew($id);
            $result = $this->sitesService->update($sites,$data);
            if($result){
                $notification = array(
                'message' => 'You have successfully updated site!',
                'alert-type' => 'success'
                );
            \Log::info($request->getHttpHost().":Updated site request for site_id=".$id);
                return redirect()->route('sites.list')->with($notification);
            } else {
                return redirect()->back()->withInput();
            }

      } catch (\Illuminate\Database\QueryException $ex) {
        \Log::error($request->getHttpHost().": Update site request failed with id=".$id.", Error=".$ex->getMessage());
      // return redirect()->back()->withInput();
      }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function ajaxgetsites()
    {
        return $this->sitesService->ajaxgetlist();
    }

    public function getusers(Request $request)
    {
        $site_name = Sites::find($request->site_id)->site_name;
        return view('admin::sites.users-list',compact('site_name'));
    }

    public function ajaxgetsiteusers()
    {
        $site_id = (isset($_GET['site_id'])) ? $_GET['site_id'] : epcache('site_id');
        return $this->userService->adminajaxgetlist($site_id);
    }


    public function payment_invoices()
    {
        return view('invoices.payment_invoices');
    }

    public function ajaxgetsitesinvoices()
    {
        return $this->sitesService->ajaxgetsitesinvoices();
    }

    public function monthly_invoice(Request $request)
    {
            $data = $request->only('site_id', 'inv_month', 'inv_year');
            if ($data['inv_month'] == '' || $data['inv_year'] == '' || $data['site_id'] == '') {
                return response()->json(['success' => false,'message' => 'Please select all these fields']);
            }
            if((date('m') < $data['inv_month'] && ( date('Y') <= $data['inv_year']) ) || (date('m') == $data['inv_month'] && ( date('Y') <= $data['inv_year']) ))
            {
                return response()->json(['success' => false,'message' => 'Invoice can not be generated for current/future month']);
            }
            else{ //Only past months invoice can be generated
                $result = $this->sitesService->monthly_invoice_generate($data);
            }
            if ($result['success'] == 0) {
                return response()->json(['success' => false,'message' => $result['message']]);
            } else {
                return response()->json(['success' => true,'message' => $result['message']]);
            }
    }

    public function getColorThemeById(Request $request)
    {
        $result = $this->sitesService->getColorThemeById($request->id);
        if($result){
            return response()->json(['success' => true,'message' => $result]);
        }
    }

    public function getdetails(Request $request)
    {
        $site_id = $request->site_id;
        $site = $this->sitesService->firstornew($site_id);
        $siteVirtualAccInfo = $this->sitesService->getSiteVirtualAccInfo($site->id);
        $onboardStatus = getOnboardStatus($site_id);
        $siteDocsExists = DB::table('site_docs')->where('site_id',$site_id)->exists();

        return view('sites.site-details',compact('site','siteVirtualAccInfo','onboardStatus','siteDocsExists'));
    }

    public function getSiteAgreements(Request $request)
    {
        return $this->sitesService->getSiteAgreements($request->site_id);
    }

    public function getSiteInvoices(Request $request)
    {
        return $this->sitesService->getSiteInvoices($request->site_id);
    }

    public function ajaxgettransactions(Request $request)
    {
        $userid = $request->site_id;
        $ac_type = -1;
        return $this->escrowService->ajaxgetlist($userid,$ac_type);
    }

    public function ajaxgetfeetransactions(Request $request)
    {
        $userid = $request->site_id;
        $ac_type = -2;
        return $this->escrowService->ajaxgetlist($userid,$ac_type);
    }

    public function ajaxgetsitedeals()
    {
        $site_id = (isset($_GET['site_id'])) ? $_GET['site_id'] : epcache('site_id');
        return $this->sitesService->ajaxgetsitedeals($site_id);
    }

    public function updateSiteStatus(Request $request)
    {
        $site         = Sites::find($request->site_id);
        $site->status = $request->status;
        $site->save();
        \Log::info("Updated status for site id : ".$request->site_id." to status :".$request->status);
        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function onboardingDetails($id)
    {
        $siteInfo = DB::table('agreements as agrmnt')->leftJoin('sites', 'sites.id', '=', 'agrmnt.site_id')->leftJoin('users', 'users.id', '=', 'agrmnt.user_id')->leftJoin('site_kyc as skyc', 'skyc.site_id', '=', 'sites.id')->leftJoin('site_docs as sdocs', 'sdocs.site_id', '=', 'sites.id')
        ->leftJoin('kyc', 'kyc.user_id', '=', 'agrmnt.user_id')
        ->select('users.name','users.email','users.mobile_no','kyc.pan as user_pan',DB::raw('skyc.pan as site_pan'),DB::raw('skyc.pan_status as site_pan_status'),'skyc.gstin','skyc.gstin_status as site_gstin_status','skyc.cin','skyc.directors','skyc.address','agrmnt.id as agreement_id','agrmnt.url','sites.id as site_id','sites.site_name','sites.onboard_nda','sites.onboard_fee','sites.onboard_monthly_fee',DB::raw('GROUP_CONCAT(sdocs.filepath) as filepaths'),DB::raw('GROUP_CONCAT(sdocs.doc_type) as doc_types'))->where('agrmnt.site_id', $id)->where('agrmnt.doc_type', 8)->groupBy('users.name','users.email','users.mobile_no','kyc.pan','skyc.pan','skyc.pan_status','skyc.gstin','skyc.gstin_status','skyc.cin','skyc.directors','skyc.address','agrmnt.id','agrmnt.url','sites.id','sites.site_name')->orderBy('agrmnt.id', 'desc')->first();

        if (isset($siteInfo)) {
            $filePaths = explode(',', $siteInfo->filepaths ?? '');
            $docTypes = explode(',', $siteInfo->doc_types ?? '');

            $panFilepath = null;
            $coiFilepath = null;
            $gstinFilepath = null;
            $otherFilepath = null;

            foreach ($docTypes as $index => $docType) {
                if (isset($filePaths[$index])) {
                    switch ($docType) {
                        case 'pan':
                            $panFilepath = $filePaths[$index];
                            break;
                        case 'gstin':
                            $gstinFilepath = $filePaths[$index];
                            break;
                        case 'coi':
                            $coiFilepath = $filePaths[$index];
                            break;
                        case 'other':
                            $otherFilepath = $filePaths[$index];
                            break;
                    }
                }
            }
        } else {
            \Log::info('No matching site info found for site_id: ' . $id);
            $filePaths = [];
            $docTypes = [];
            $panFilepath = null;
            $coiFilepath = null;
            $gstinFilepath = null;
            $otherFilepath = null;
        }

        $authSignatoryUsersInfo =  DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')->leftjoin('kyc','kyc.user_id','users.id')->leftjoin('userbanks','userbanks.user_id','users.id')
                ->select('users.id as uid', 'users.name','users.email','users.mobile_no','kyc.pan','kyc.pan_status','userbanks.bank_name','userbanks.bank_account','userbanks.bank_ifsc')
                ->where('roles.site_id', $id)
                ->where('roles.name','admin')
                ->get();

        $onboardStatus = getOnboardStatus($id);
        $agrmnt = DB::table('agreements as agrmnt')->leftjoin('sites','sites.id','agrmnt.site_id')->select('agrmnt.url')->where('agrmnt.site_id',$id)->where('agrmnt.doc_type',9)->orderBy('agrmnt.id','desc')->first();

        return view('onboard.details',compact('siteInfo','panFilepath','gstinFilepath','coiFilepath','otherFilepath','agrmnt','onboardStatus','authSignatoryUsersInfo'));
    }
} //end
