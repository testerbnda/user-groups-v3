<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\Sites;
use Modules\Paymentgatyway\Entities\Payment;
use Spatie\Permission\Models\Role;
use Spatie\WebhookServer\WebhookCall;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Modules\Deal\Entities\DealTypeSubDealTypeMapping;
use Modules\Deal\Entities\SubDealTypeFieldMapping;

//use Session;

//use Amqp;
//use DB;
// use Auth;

if (!function_exists('jsonResponse')) {
    /**
     * json response for api calls
     *
     * @param $message
     * @param array $data
     * @param bool $success
     *
     * @return JsonResponse
     */
    function jsonResponse(string $message, array $data = [], bool $success = true)
    {
        $response = ['success' => $success, 'message' => $message];

        if ($success) {
            $response['data'] = $data;
            return new JsonResponse($response, 200);
        } else {
            return new JsonResponse($response, 400);
        }

    }
}

if (!function_exists('numberToCurrency')) {
    /**
     * @param string $message
     * @return JsonResponse
     */
    function numberToCurrency($number)
    {
        $decimal   = (string) ($number - floor($number));
        $decimal   = round($decimal, 2);
        $money     = floor($number);
        $length    = strlen($money);
        $delimiter = '';
        $money     = strrev($money);

        for ($i = 0; $i < $length; $i++) {
            if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
                $delimiter .= ',';
            }
            $delimiter .= $money[$i];
        }

        $result  = strrev($delimiter);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);

        if ($decimal != '0') {
            $result .= $decimal;
        } else {
            $result .= '.00';
        }

        return '₹ ' . $result;
    }
}

if (!function_exists('numberToCurrency_without_decimal')) {
    /**
     * @param string $message
     * @return JsonResponse
     */
    function numberToCurrency_without_decimal($number)
    {
        $amount = number_format(round($number));
        return '₹ ' . $amount;
    }
}

if(!function_exists('numberToINR')){
    function numberToINR($number,$hasDecimal = true, $hasCurrency = false)
    {
        //call this function - numberToInr($amount,0,1)
        $currency = '₹';  $negativeSign = '-'; $number1 = 0;
        $number1 = ($number < 0) ? substr($number,1) : $number; //check =>if $number is postive or negative
        $decimal   = (string) ($number1 - floor($number1));
        $decimal = round($decimal,2);
        $decimal = number_format($decimal,2);
        $money     = floor($number1);
        $length    = strlen($money);
        $delimiter = '';
        $money     = strrev($money);
        for ($i = 0; $i < $length; $i++) {
            if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
                $delimiter .= ',';
            }
            $delimiter .= $money[$i];
        }
        $result  = strrev($delimiter);
        if($hasDecimal == 1){
            $decimal = preg_replace("/0\./i", ".", $decimal);
            $decimal = substr($decimal, 0, 3);
            if ($decimal != '0') {
                $result .= $decimal;
                $result = ($number < 0 ) ? $negativeSign.$result : $result;
            } else {
                $result = ($number < 0 ) ? $negativeSign.$result : $result;
                $result .= '.00';
            }
            if($hasCurrency == 1){
                return $currency.$result;
            }else{
                return $result;
            }
        }else{
            $result = number_format(round($number));
            if($hasCurrency == 1){
                return $currency.$result;
            }else{
                return $result;
            }
        }
    }
}

if (!function_exists('messageResponse')) {
    /**
     * @param string $message
     * @return JsonResponse
     */
    function messageResponse(string $message)
    {
        return new JsonResponse(['success' => true, 'message' => $message]);
    }
}

if (!function_exists('uploadFile')) {
    /**
     * upload file to storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $name
     * @return false|string
     */
    function uploadFile(UploadedFile $file, string $path, string $name = '')
    {
        $fileName = $name ?: str_random(35) . time() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($path, $fileName);
    }
}

if (!function_exists('uploadFileToS3')) {
    /**
     * upload file to storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $name
     * @return false|string
     */
    function uploadFileToS3(UploadedFile $file, string $path, string $name = '')
    {
        $fileName = $name ?: str_random(35) . time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('s3')->put($path . '/' . $fileName, file_get_contents($file));

        return Storage::disk('s3')->url($path . '/' . $fileName);
    }
}

if (!function_exists('randomString')) {
    /**
     * random unique string
     *
     * @param int $chars
     * @return string
     */
    function randomString(int $chars = 25)
    {
        return str_random($chars - 10) . time();
    }
}

if (!function_exists('dateTime')) {
    /**
     * current date time of given format
     *
     * @param string $format
     * @return false|string
     */
    function dateTime($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }
}

if (!function_exists('carbon')) {
    /**
     * create carbon object from date time string
     *
     * @param string $datetime
     * @return Carbon
     */
    function carbon(string $datetime): Carbon
    {
        return Carbon::parse($datetime);
    }
}

if (!function_exists('can_escrow')) {
    function can_escrow($permission)
    {
        $domain  = $_SERVER['HTTP_HOST'];
        $result  = DB::table('sites')->select('id')->where('site_domain', $domain)->first();
        $site_id = $result->id;

        $user = \Auth::user();
        if (Auth::check()) {
            //$userRole = $user->roles->where('site_id',$site_id)->first();
            $userRole = $user->roles->all();
            if ($userRole != null) {
                //$role = Role::findByName($userRole->name);
                foreach ($userRole as $role) {
                    if ($role->hasPermissionTo($permission)) {
                        // echo $permission.'<br/>';
                        return true;
                    }
                }

            }

            return false;
        } else {
            return false;
        }

    }
}

if (!function_exists('check_user_type')) {
    function check_user_type($dealid, $userid)
    {
        $result = DB::table('deal_user')->select('user_type')->where('deal_id', $dealid)->where('user_id', $userid)->where('user_type', 2)->first();
        if ($result == null) {
            return false;
        } else {
            return true;
        }

    }
}

if (!function_exists('get_current_loggedin_user_siteid')) {
    function get_current_loggedin_user_siteid($val = 0)
    {
        $roles = Auth::user()->roles->pluck('name')->toArray();
        if (in_array('Superadmin', $roles)) {
            return Role::pluck('site_id')->toArray();
        } else {
            return Auth::user()->roles->pluck('site_id')->toArray();
        }

    }
}

if (!function_exists('get_roles_for_user_listing')) {
    function get_roles_for_user_listing($val = 0)
    {
        $siteid    = Auth::user()->roles->pluck('site_id', 'site_id')->toArray();
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (in_array('admin', $rolesname)) {
            $admin        = 'admin';
            $manager      = 'manager';
            $trustee      = 'trustee';
            return $roles = Role::where('roles.name', 'not like', '%' . $admin . '%')->where('roles.name', 'not like', '%' . $manager . '%')->where('roles.name', 'not like', '%' . $trustee . '%')->whereIn('roles.site_id', $siteid)->pluck('id', 'id')->toArray();
        } elseif (in_array('manager', $rolesname)) {

            $manager      = 'manager';
            $trustee      = 'trustee';
            return $roles = Role::where('roles.name', 'not like', '%' . $manager . '%')->where('roles.name', 'not like', '%' . $trustee . '%')->whereIn('roles.site_id', $siteid)->pluck('id', 'id')->toArray();
        } else {
            return $roles = Role::whereIn('roles.site_id', $siteid)->pluck('id', 'id')->toArray();
        }

    }

}

if (!function_exists('get_users')) {
    function get_users($site_id)
    {
        $user_list = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.id as uid', 'users.name as uname')
            ->where('roles.site_id', $site_id)
            ->orderBy('users.id', 'DESC')
            ->groupby('users.id')
            ->get();
        foreach ($user_list as $key => $value) {
            echo '<option value="' . $value->uid . '">' . $value->uname . '</option>';
        }

    }
}

if (!function_exists('authenticate_user')) {
    function authenticate_user($site_id, $uid)
    {
        $user = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.name as name', 'users.email')
            ->where('roles.site_id', $site_id)
            ->where('users.id', $uid)
            ->first();
        if ($user) {
            return 1;
        }
    }
}

if (!function_exists('getescrowbalance')) {
    function getescrowbalance($rupeesign = null)
    {
        $user = \Auth::user();
        if (is_admin() || is_manager() || can_escrow('site_admin_access')) {
            $site_id = epcache('site_id');
            $user_id = $site_id;
            $ac_type = -1;
        } else {
            $user_id = $user->id;
            $ac_type = 0;
        }

        if ($user) {

            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('balance');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return ($rupeesign == null) ? numberToInr($escrowbalance->balance,1,1) : numberToInr($escrowbalance->balance,1,0);
            } else {
                return ($rupeesign == null) ? numberToInr('0',1,1) : 0;
            }
        } else {
            return ($rupeesign == null) ? numberToInr('0',1,1) : 0;
        }

    }
}

if (!function_exists('getcollectionbalance')) {
    function getcollectionbalance($id)
    {
        if ($id) {
            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $id)->where('ac_type', 1)->sum('balance');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return numberToCurrency($escrowbalance);
            } else {
                return numberToCurrency('0');
            }
        } else {
            return numberToCurrency('0');
        }

    }
}

if (!function_exists('checkcollectionbalance')) {
    function checkcollectionbalance($id)
    {
        if ($id) {
            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $id)->where('ac_type', 1)->sum('balance');
            if ($escrowbalance != null) {
                return $escrowbalance;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }
}

if (!function_exists('replace_mail_data')) {
    function replace_mail_data($data, $template_id, $temp_type)
    {
        $templates = DB::table('templates')->where('id', $template_id)->first();
        if(empty($data)){
            return $templates->template_format;
        }
        if ($temp_type == 'temp_body') {
            $string = $templates->template_format;
        } else if ($temp_type == 'temp_name') {
            $string = $templates->template_name;
        }
        $array_keys = array_keys($data);
        $arr        = array();
        foreach ($array_keys as $key => $value) {
            if ($temp_type == 'temp_body') {
                $arr[] = '[' . $value . ']';
            } else if ($temp_type == 'temp_name') {
                $arr[] = '[' . $value . ']';
            }
        }
        $search = $arr;

        $newArray = array();
        foreach ($search as $bad) {
            if (stristr($string, $bad) !== false) {
                $newArray[] = $bad;
            }
        }
        // Replace words
        $replace = array();
        foreach ($array_keys as $keyArr => $valueArr) {
            if ($temp_type == 'temp_body') {
                $arrMatch = in_array('[' . $valueArr . ']', $newArray);
            } else if ($temp_type == 'temp_name') {
                $arrMatch = in_array('[' . $valueArr . ']', $newArray);
            }
            if ($arrMatch) {
                $replace[] = $data[$valueArr];
            } else {
                $replace[] = '';
            }
        }

        $arrFilter   = array_values(array_filter($replace));
        return $html = str_replace($newArray, $arrFilter, $string);

    }
}

if (!function_exists('get_transaction_status')) {
    function get_transaction_status($value)
    {
        switch ($value) {
            case "0":
                return '<span class="badge badge-outline-warning">Pending</span>';
                break;
            case "1":
                return '<span class="badge badge-outline-primary">Success</span>';
                break;
            case "4":
                return '<span class="badge badge-outline-success">Approved</span>';
                break;
            case "5":
            case "7":
                return '<span class="badge badge-outline-danger">Rejected</span>';
                break;
            case "6":
                return '<span class="badge badge-outline-success">Processed</span>';
                break;
            case "8":
                return '<span class="badge badge-outline-info">Pending Payout</span>';
                break;
            case "9":
                return '<span class="badge badge-outline-dark">Further Action Needed</span>';
                break;
            default:
                return '-';
        }
    }
}

if (!function_exists('get_gateway_id')) {
    function get_gateway_id()
    {
        $domain  = $_SERVER['HTTP_HOST'];
        $result  = DB::table('sites')->select('id')->where('site_domain', $domain)->first();
        $site_id = $result->id;
        // dd($site_id);

        $getGateway = DB::table('site_gateways')->select('payment_gateways.id as id')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 0)->first();
        if (isset($getGateway)) {
            return $getGateway->id;
        } else {
            return 0;
        }
    }
}

if (!function_exists('getEaaaStatus')) {
    function getEaaaStatus($user_id)
    {
        $agreement = DB::table('agreements')->leftjoin('agreement_signings as sign', 'sign.agreement_id', '=', 'agreements.id')->select('agreements.*', 'sign.status', 'sign.etype')->where('sign.signer_id', $user_id)->where('sign.etype', 'agreement')->get();
        if (isset($agreement)) {
            return $agreement;
        }
    }
}

/*if(!function_exists('getEaaaStatus')){
function getEaaaStatus($user_id){
$agreement = DB::table('agreement_signings as sign')->leftjoin('agreements as agr','sign.agreement_id','=','agr.id')->leftjoin('dealdocs as dd','sign.agreement_id','=','dd.id')->select('agr.site_id as agrsiteid','agr.url as agrurl','agr.platform_id as agrplatformid','agr.id as agrid','dd.doc_path as ddurl','dd.platform_id as ddplatformid','dd.id as ddid','sign.status','sign.etype')->where('sign.signer_id',$user_id)->get();
if(isset($agreement)){
return $agreement;
}
}
}*/

if (!function_exists('get_states')) {
    function get_states()
    {
        $numRows = DB::table('states')->orderBy('id', 'asc')->get();
        return $numRows;
    }
}

if (!function_exists('get_current_user_role')) {
    function get_current_user_role()
    {
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (isset($rolesname)) {return $rolesname;} else {return 0;}
    }
}

if (!function_exists('deal_users')) {
    function deal_users()
    {
        $siteid = Auth::user()->roles->pluck('site_id', 'site_id')->first();
        $user   = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.name as name', 'users.id')
            ->where('roles.site_id', $siteid)
            ->where('roles.name', 'user')
            ->get();
        if ($user) {
            return $user;
        }
    }
}
if (!function_exists('sub_deal_type')) {
    function subDealType()
    {
        $siteId = Auth::user()->roles->pluck('site_id', 'site_id')->first();
        $subDealType = DealTypeSubDealTypeMapping::select('id','sub_deal_type')
        ->where('site_id', $siteId )
        ->get()->toArray();
        if ($subDealType) {
            return $subDealType;
        }

    }
}

if (!function_exists('sub_deal_field')) {
    function subDealField($subDealId)
    {
        $subDealField = DB::table('sub_deal_type_field_mappings AS sb')
        ->where('sub_deal_type_map_id', $subDealId )
        ->select('sb.*')
        ->get()->toArray();

        if ($subDealField) {
            return $subDealField;
        }

    }
}

if (!function_exists('epcache')) {
    function epcache($key)
    {
        return Cache::get($_SERVER['HTTP_HOST'] . '_' . $key);
    }
}

if (!function_exists('monthYear')) {
    function monthYear($date)
    {

        $result = "";

        $convert_date = strtotime($date);
        $month        = date('F', $convert_date);
        $year         = date('Y', $convert_date);
        $name_day     = date('l', $convert_date);
        $day          = date('j', $convert_date);

        $result = $month . "-" . $year;

        return $result;
    }
}

if (!function_exists('numberToWord')) {

    function numberToWord($number)
    {
        $new_num = floor($number);
        $no      = floor($number);
        //$point = round($number - $no, 2) * 100;
        $point    = ($number - $no) * 100;
        $point    = round($point);
        $hundred  = null;
        $digits_1 = strlen($no);
        $i        = 0;
        $str      = array();
        $words    = array('0' => '', '1'          => 'One', '2'       => 'Two',
            '3'                   => 'Three', '4'     => 'Four', '5'      => 'Five', '6' => 'Six',
            '7'                   => 'Seven', '8'     => 'Eight', '9'     => 'Nine',
            '10'                  => 'Ten', '11'      => 'Eleven', '12'   => 'Twelve',
            '13'                  => 'Thirteen', '14' => 'Fourteen',
            '15'                  => 'Fifteen', '16'  => 'Sixteen', '17'  => 'Seventeen',
            '18'                  => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30'                  => 'Thirty', '40'   => 'Forty', '50'    => 'Fifty',
            '60'                  => 'Sixty', '70'    => 'Seventy',
            '80'                  => 'Eighty', '90'   => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number  = floor($no % $divider);
            $no      = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural  = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                $str[]   = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }

        }
        $str    = array_reverse($str);
        $result = implode('', $str);
        if ($point > 20) {
            $points = " " . $words[$point - $point % 10] . " " . $words[$point = $point % 10];
        } elseif ($point == 0) {
            $points = " Zero ";
        } else {
            $points = " " . $words[$point];
        }
        //$points = ($point) ? " " . $words[$decPoint] . " " . $words[$point = $point % 10] : '';
        if ($new_num == 0) {
            $rup = ' Zero Rupees And ';
        } elseif ($new_num == 1) {
            $rup = ' Rupee And ';
        } else {
            $rup = ' Rupees And ';
        }
        echo $result . $rup . $points . " Paise";
    }
}

if (!function_exists('payment_requests')) {
    function payment_requests($uid, $paylink)
    {
        $unique_id = md5(uniqid() . time());
        $result    = DB::table('payment_requests')->insertGetId([
            "site_id"      => epcache('site_id'),
            "user_id"      => $uid,
            "payment_code" => $unique_id,
            "payment_url"  => $paylink,
            "created_at"   => date('Y-m-d H:i:s'),
        ]);
        $payCode = DB::table('payment_requests')->where('id', $result)->first();
        return 'http://' . $_SERVER['HTTP_HOST'] . '/payment?ps=' . $payCode->payment_code;
    }
}

if (!function_exists('get_specific_deal_users')) {
    function get_specific_deal_users($dealid)
    {
        $user = DB::table('deal_user')
            ->leftJoin('users', 'deal_user.user_id', '=', 'users.id')
            ->select('users.name as name', 'users.id', 'deal_user.pivot_field1', 'deal_user.pivot_field2')
            ->where('deal_user.deal_id', $dealid)
            ->where('deal_user.user_type', 2)
            ->get();
        if ($user) {
            return $user;
        }
    }
}

if (!function_exists('generatesitesEscrowAcNo')) {
    function generatesitesEscrowAcNo($input, $pad_len = 2, $prefix = null)
    {
        if (is_string($prefix)) {
            return sprintf("%s%s", $prefix, str_pad($input, $pad_len, "0", STR_PAD_LEFT));
        }

        return str_pad($input, $pad_len, "0", STR_PAD_LEFT);
    }
}

if (!function_exists('siteNachIdGenerator')) {
    function siteNachIdGenerator($idtype, $pid)
    {
        $site_code = env('APP_ENV', 'local') == 'prod' ? epcache('site_code') : epcache('site_code') . 'UAT';

        $type = 0;
        $id   = 1;
        if ($idtype == 0 || $idtype == 1) {
            // CONSUMER ID & MANDATE REGISTRATION TXN ID
            $type   = $idtype;
            $lastId = DB::table('enach_registration')->orderby('id', 'desc')->first();
            $id += isset($lastId) ? $lastId->id : 0;
        } else if ($idtype == 2) {
            // TXN SCHEDULING TXN ID
            $type   = '2';
            $lastId = DB::table('enach_txn_scheduling')->orderby('id', 'desc')->first();
            $id += isset($lastId) ? $lastId->id : 0;
        }

        return $site_code . $type . '0' . $pid . '0' . $id;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (in_array("admin", $rolesname)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_superadmin')) {
    function is_superadmin()
    {
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (in_array("Superadmin", $rolesname)) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('is_manager')) {
    function is_manager()
    {
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (in_array("manager", $rolesname)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_site_info')) {
    function get_site_info()
    {
        $sites = Sites::where('site_domain', $_SERVER['HTTP_HOST'])->first();
        if ($sites) {
            return $sites;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_pan_number')) {
    function get_pan_number($user_id)
    {
        $get_pan_number = DB::table('kyc')->where('user_id', $user_id)->first();
        if ($get_pan_number) {
            return $get_pan_number->pan;
        } else {
            return false;
        }
    }
}

if (!function_exists('getSites')) {
    function getSites()
    {
        return $getSites = Sites::leftjoin('roles', 'roles.site_id', 'sites.id')->join('model_has_roles', 'model_has_roles.role_id', 'roles.id')->select('sites.id', 'sites.site_group', 'sites.site_code','sites.site_name')->whereNotNull('sites.site_group')->where('model_has_roles.model_id', Auth::id())->get();
    }
}

if (!function_exists('getPendingData')) {
    function getPendingData()
    {
        $records1 = DB::select(DB::raw("select site_id,count(1) as due from virtual_escrow_txns where type='debit' and bank_id is not null and status=0 group by site_id"));
        $data = array();
        $totalDue = 0;
        foreach($records1 as $records) {
            $data[$records->site_id] =  $records->due;
            $totalDue += $records->due;
        }
        $data[0] = $totalDue;
        return $data;
    }
}

if (!function_exists('get_sites')) {
    function get_sites()
    {

        $data = DB::table('model_has_roles')->join('roles', 'model_has_roles.role_id', 'roles.id')->leftjoin('sites', 'roles.site_id', 'sites.id')->select('sites.id', 'sites.site_group', 'sites.site_name')->where('roles.name', 'manager')->where('model_has_roles.model_id', Auth::id())->get();
        if ($data) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_site_admins_email')) {
    function get_site_admins_email($site_id)
    {

        $data = DB::table('site_admins')->where([['site_id', $site_id], ['status', 1]])->pluck('email')->toArray();
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_all_sites')) {
    function get_all_sites()
    {

        $data = Sites::all()->sortBy('site_name');
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_all_billing_plans')) {
    function get_all_billing_plans()
    {

        $data = DB::table('billing_plans')->get();
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_logo')) {
    function get_logo()
    {
        $siteInfo = Sites::leftjoin('sites_meta as sm','sm.sites_id','sites.id')->select('sm.sites_logo_image')->where('sites.site_domain', $_SERVER['HTTP_HOST'])->first();
        return isset($siteInfo) ? $siteInfo->sites_logo_image : '';
    }
}

if (!function_exists('get_countries')) {
    function get_countries()
    {
        $countries = DB::table('countries')->orderBy('id', 'asc')->get();
        return $countries;
    }
}

if (!function_exists('get_enach_beneficiary')) {
    function get_enach_beneficiary($userid)
    {
        $data = DB::select(DB::raw('select d.id as deal_id,d.deal_ref_id,a.id as acc_id from deals d, deal_user du, virtual_escrow_accounts a where d.id=du.deal_id and d.id=a.user_id and a.ac_type>1 and a.site_id=' . epcache('site_id') . ' and du.user_id=' . $userid));
        return $data;
    }
}

function webhookCall($data)
{
    try{
        \Log::info("Webhook Payload: ".json_encode($data));

        $envData = env('APP_ENV', 'local');
        if (isset($data['webhook_url']) && $data['webhook_url'] != '') {
            $webhookURL = $data['webhook_url'];
        } else {
            $webhookURL = $envData == 'prod' ? 'https://dev-webhook.klubworks.com/escrowpay/transactions' : 'https://dev-webhook.klubworks.com/escrowpay/transactions';
        }
        unset($data['webhook_url']);

        WebhookCall::create()
        ->doNotSign()
        ->url($webhookURL)
        ->payload($data)
        ->dispatch();
    } catch (\Exception $ex){
        \Log::error("Error in webhookCall(): ".$ex->getMessage());
    }

}

/*if (!function_exists('queue_publish')){
function queue_publish($message,$queue)
{
return Amqp::publish('webhook', $message, ['queue' => $queue]);
}
}*/

if (!function_exists('get_badges')) {
    function get_badges($value)
    {
        switch ($value) {
            case "DEBUG" :
                return '<span class="badge badge-dim badge-dark">' . $value . '</span>';
                break;
            case "INFO":
                return '<span class="badge badge-dim badge-info">' . $value . '</span>';
                break;
            case "NOTICE":
                return '<span class="badge badge-dim badge-danger">' . $value . '</span>';
                break;
            case "WARNING":
                return '<span class="badge badge-dim badge-warning">' . $value . '</span>';
                break;
            case "ERROR":
                return '<span class="badge badge-dim badge-danger">' . $value . '</span>';
                break;
            case "CRITICAL":
                return '<span class="badge badge-dim badge-danger">' . $value . '</span>';
                break;
            case "ALERT":
                return '<span class="badge badge-dim badge-danger">' . $value . '</span>';
                break;
            case "EMERGENCY":
                return '<span class="badge badge-dim badge-danger">' . $value . '</span>';
            default:
                return '<span class="badge badge-dim badge-primary">' . $value . '</span>';
        }

    }

    if (!function_exists('encrypt_decrypt')) {
        function encrypt_decrypt($action, $string)
        {

        $data = explode("?",$string);
        $string = $data[0];

            $output = false;

            $encrypt_method = "AES-256-CBC";
            $secret_key     = env('APP_ENCRYPT_DECRYPT_KEY', 'aDFEN0JaZmd1RVEzNFpMVk5j');
            $secret_iv      = base64_encode(Session::getId());

            // hash
            $key = base64_encode(hash('sha256', $secret_key));

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

             //   $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encrypt_method));
            if ($action == 'encrypt') {
                 $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                 $output = base64_encode($output);
            } else if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

            return $output;
        }

    }

}

if (!function_exists('get_site_usage')) {
    function get_site_usage($siteID)
    {
        //$data = DB::select(DB::raw('select month(created_at) month,year(created_at) year,api_type,sum(cost) cost,count(1) count from site_billings where site_id='.$siteID.' group by month(created_at),year(created_at),api_type'));

        $data = DB::select(DB::raw('select `site_id`, DATE_FORMAT(created_at, "%M %Y") new_date, sum(CASE WHEN api_type = "PAN" THEN cost else 0 END) as pan_cost, sum(CASE WHEN api_type = "PAN" THEN 1 else 0 END) as pan_count, sum(CASE WHEN api_type = "BANK" THEN cost else 0 END) as bank_cost, sum(CASE WHEN api_type = "BANK" THEN 1 else 0 END) as bank_count, sum(CASE WHEN api_type = "ESIGN" THEN cost else 0 END) as esign_cost, sum(CASE WHEN api_type = "ESIGN" THEN 1 else 0 END) as esign_count from `site_billings` where `site_id` = '.$siteID.' GROUP BY new_date '));
        return $data;
    }
}

if (!function_exists('is_transactions')) {
    function is_transactions($user_id,$ac_type=3)
    {
        $escrow_virtual_id = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('id')->id;
                    $transactions = DB::table('virtual_escrow_txns')->orwhere('party_ac_id', $escrow_virtual_id)->orwhere('account_id', $escrow_virtual_id)->first();
                    if ($transactions == null) {
                      return true;
                    } else {
                      return false;
                    }

    }
}

if (!function_exists('getclearingbalance')) {
    function getclearingbalance($rupeesign = null)
    {
        $user = \Auth::user();
        if (is_admin() || is_manager() || can_escrow('site_admin_access')) {
            $site_id = epcache('site_id');
            $user_id = $site_id;
            $ac_type = -1;
        } else {
            $user_id = $user->id;
            $ac_type = 0;
        }

        if ($user) {
            @
            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('clearing_amount');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return ($rupeesign == null) ? numberToCurrency($escrowbalance->clearing_amount) : $escrowbalance->clearing_amount;
            } else {
                return ($rupeesign == null) ? numberToCurrency('0') : 0;
            }
        } else {
            return ($rupeesign == null) ? numberToCurrency('0') : 0;
        }

    }
}

if (!function_exists('check_maker_checker')) {
    function check_maker_checker($id)
    {
        if($id==null){
            return false;
        }
            $data = DB::table('payment_gateways')->where('id', $id)->first();
            if ($data->maker_checker == 1) {
                return true;
            } else {
                return false;
            }

    }
}


if (!function_exists('aes128EncryptGlobal')) {
   function aes128EncryptGlobal($str)
    {
        $plaintext  = $str;
        $cipher     = "AES-128-ECB";
        $key = "YBRdxrvdg6XQgjXGUcceaPHxyySCTUVhGsG69wV2VykVe7qQz2aXfu7unHGEJvkeBS";
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options = 0, $iv = "");
        return $ciphertext;
    }
}

if (!function_exists('aes128DecryptGlobal')) {
   function aes128DecryptGlobal($str)
    {
        $encrypttext = str_replace(' ', '+', $str);
        $cipher = "AES-128-ECB";
        $key = "YBRdxrvdg6XQgjXGUcceaPHxyySCTUVhGsG69wV2VykVe7qQz2aXfu7unHGEJvkeBS";
        $decrypttext = openssl_decrypt($encrypttext, $cipher, $key, $options=0, $iv="");
        return $decrypttext;
    }
}


if (!function_exists('get_bank_user_role_wise'))
{
    function get_bank_user_role_wise($site_id,$role_name,$order)
    {
        $user_bank = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->leftJoin('userbanks', 'userbanks.user_id', '=', 'users.id')
            ->select('userbanks.id as bank_id','users.id as user_id')
            ->where('roles.site_id', $site_id)
            ->where('roles.name', $role_name)
            ->orderBy('users.id', $order)
            ->first();
        if (isset($user_bank)) {
            return $user_bank;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_deal_user_info')) {
    function get_deal_user_info($dealid, $userid)
    {
        $result = DB::table('deal_user')->where('deal_id', $dealid)->where('user_id', $userid)->where('user_type', '!=', 3)->first();
        if ($result == null) {
            return false;
        } else {
            return $result;
        }

    }
}


if (!function_exists('user_has_bid')) {
    function user_has_bid($dealid, $userid)
    {
        $result = DB::table('deal_user')->where('deal_id', $dealid)->where('user_id', $userid)->where('user_type', '!=', 3)->first();
        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('check_user_type_lendor')) {
    function check_user_type_lendor($dealid, $userid)
    {
        $result = DB::table('deal_user')->select('user_type')->where('deal_id', $dealid)->where('user_id', $userid)->where('user_type', 3)->first();
        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_lender_info')) {
    function get_lender_info($site_id)
    {
        $lendors = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->rightJoin('user_info', 'users.id', '=', 'user_info.user_id')
                    ->select('user_info.*')
                    ->where('roles.site_id', $site_id)
                    ->orderBy('user_info.name', 'ASC')->get();
        if ($lendors == null) {
            return false;
        } else {
            return $lendors;
        }
    }
}

if (!function_exists('get_bank_code')) {
    function get_bank_code($site_id = null)
    {
        $site_id = isset($site_id) ? $site_id : epcache('site_id');
        $getGateway = DB::table('site_gateways')->select('payment_gateways.gateway_code as gateway_code')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 1)->first();
        if (isset($getGateway->gateway_code)) {
            return $getGateway->gateway_code;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_gateway_code')) {
    function get_gateway_code($domain)
    {
        $result  = DB::table('sites')->select('id')->where('site_domain', $domain)->first();
        $site_id = $result->id;
        // dd($site_id);

        $getGateway = DB::table('site_gateways')->select('payment_gateways.gateway_code as gateway_code')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 0)->first();
        if (isset($getGateway->gateway_code)) {
            return $getGateway->gateway_code;
        } else {
            return null;
        }
    }
}

if (!function_exists('getescrowbalancefee')) {
    function getescrowbalancefee($rupeesign = null)
    {
        $user = \Auth::user();
        if (is_admin() || is_manager() || can_escrow('site_admin_access')) {
            $site_id = epcache('site_id');
            $user_id = $site_id;
            $ac_type = -2;
        } else {
            $user_id = $user->id;
            $ac_type = 0;
        }

        if ($user) {

            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('balance');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return ($rupeesign == null) ? numberToInr($escrowbalance->balance,1,1) : $escrowbalance->balance;
            } else {
                return ($rupeesign == null) ? numberToInr('0',1,1) : 0;
            }
        } else {
            return ($rupeesign == null) ? numberToInr('0',1,1) : 0;
        }

    }
}

if (!function_exists('getclearingbalancefee')) {
    function getclearingbalancefee($rupeesign = null)
    {
        $user = \Auth::user();
        if (is_admin() || is_manager() || can_escrow('site_admin_access')) {
            $site_id = epcache('site_id');
            $user_id = $site_id;
            $ac_type = -2;
        } else {
            $user_id = $user->id;
            $ac_type = 0;
        }

        if ($user) {
            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('clearing_amount');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return ($rupeesign == null) ? numberToCurrency($escrowbalance->clearing_amount) : $escrowbalance->clearing_amount;
            } else {
                return ($rupeesign == null) ? numberToCurrency('0') : 0;
            }
        } else {
            return ($rupeesign == null) ? numberToCurrency('0') : 0;
        }

    }
}

if (!function_exists('checkAccMainEp')) {
    function checkAccMainEp($site_id,$ac_type)
    {
        $escrowAcc = DB::table('virtual_escrow_accounts')->where('site_id', $site_id)->where('user_id', $site_id)->where('ac_type', $ac_type)->first();
        if ($escrowAcc != null) {
            return 1;
        } else {
            return 0;
        }
    }
}


if (!function_exists('getUserIpAddr')) {
    function getUserIpAddr(){
        try {
            if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
               $proxy_list = explode (",", $_SERVER['HTTP_X_FORWARDED_FOR']);
               $ip = trim (end ($proxy_list));
           }elseif(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
            $proxy_list = explode (",", $_SERVER['HTTP_CLIENT_IP']);
            $ip = trim (end ($proxy_list));
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    } catch (\Exception $ex){
        \Log::error("Error in getUserIpAddr(): ".$ex->getMessage());
    }
}
}

if (!function_exists('get_user_info_by_roles')) {
    function get_user_info_by_roles($site_id)
    {
        $get_user_info = DB::table('roles as r')->join('model_has_roles as m','m.role_id','=','r.id')->join('users as u','u.id','=','m.model_id')->join('addresses as a','a.user_id','=','u.id')->join('gstin_details as g','g.user_id','=','u.id')->join('states as s','s.id','=','a.state')->join('cities as c','c.id','=','a.city')->select('u.email','g.gstin_number','a.address_line_one','a.address_line_two','a.postal_code','s.name as state_name','c.city_name')->where('r.site_id',$site_id)->where('r.name','admin')->first();
        if ($get_user_info != null) {
            return $get_user_info;
        } else {
            return false;
        }
    }
}

if(!function_exists('getFeeAccSite')){
    function getFeeAccSite()
    {
        $data = DB::table('virtual_escrow_accounts')->leftjoin('sites','sites.id','virtual_escrow_accounts.site_id')->select('sites.id','sites.site_name','sites.site_group')->where('virtual_escrow_accounts.ac_type',-2)->get();
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }
}

if(!function_exists('getFeeAccInfo')){
    function getFeeAccInfo($ac_type)
    {
        $data = DB::table('virtual_escrow_accounts as acc')->leftjoin('sites','sites.id','acc.site_id')->select('sites.id as site_id','sites.site_name','acc.escrow_ac_no','acc.bank_name','acc.ifsc_code','acc.balance','acc.ac_type')->where('acc.ac_type', $ac_type)->where('site_id',epcache('site_id'))->first();

        if($data){ return $data; }
        else{ return null; }
    }
}

if (!function_exists('get_all_sites_via_ac_type')) {
    function get_all_sites_via_ac_type($ac_type)
    {

        $data = DB::table('virtual_escrow_accounts as v')->leftjoin('sites as s','s.id','=','v.user_id')->select('s.id','s.site_name')->where('v.ac_type',$ac_type)->get();
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_all_onboarding_template_code')) {
    function get_all_onboarding_template_code($temp_type)
    {

        $data = DB::table('templates')->where('template_type',$temp_type)->get();
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

    }
}

if (!function_exists('get_first_site_admins_email')) {
    function get_first_site_admins_email($site_id)
    {

        $data = DB::table('site_admins')->where([['site_id', $site_id], ['status', 1]])->orderby('created_at','ASC')->first();
        if (!empty($data)) {
            return $data->email;
        } else {
            return null;
        }

    }
}

if (!function_exists('getFdAccbalance')) {
    function getFdAccbalance($rupeesign = null)
    {
            $site_id = epcache('site_id');
            $user_id = $site_id;
            $ac_type = -3;
            $escrowbalance = DB::table('virtual_escrow_accounts')->where('user_id', $user_id)->where('ac_type', $ac_type)->first('balance');
            if ($escrowbalance != null) {
                // echo $permission.'<br/>';
                return ($rupeesign == null) ? numberToInr($escrowbalance->balance,1,1) : $escrowbalance->balance;
            } else {
                return ($rupeesign == null) ? numberToInr('0',1,1) : 0;
            }


    }
}


if (!function_exists('get_site_admin_user_id'))
{
    function get_site_admin_user_id($site_id,$role_name,$order)
    {
        $user = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.id as user_id','users.name','users.email','users.mobile_no')
            ->where('roles.site_id', $site_id)
            ->where('roles.name', $role_name)
            ->orderBy('users.id', $order)
            ->first();
        if (isset($user)) {
            return $user;
        } else {
            return null;
        }
    }
}


if(!function_exists('get_default_theme'))
{
    function get_default_theme()
    {
        $theme = DB::table('color_themes')->where('name','Frozen Blue')->first();
        if(isset($theme)){ return $theme;}
        else{ return null; }
    }
}


if(!function_exists('checkSiteGroup'))
{
    function checkSiteGroup($id)
    {
        $siteGroup = DB::table('sites')->where('id',$id)->where('site_group','rental')->exists();
        if($siteGroup == true){
            return true;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_secret_id_key'))
{
    function get_secret_id_key($id)
    {
        $secretData = DB::table('payment_gateways')->where('id',$id)->first();
        if(isset($secretData)) { return $secretData; }
        else{ return null; }
    }
}

if (!function_exists('is_superadmin')) {
    function is_superadmin()
    {
        $rolesname = Auth::user()->roles->pluck('name', 'name')->toArray();
        if (in_array("super_admin", $rolesname)) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('get_payment_gateways')){
    function get_payment_gateways()
    {
        $pg = DB::table('payment_gateways')->where('gateway_code','like','cashfree%')->get();
        if (!empty($pg)) {
            return $pg;
        } else {
            return null;
        }
    }
}


if (!function_exists('get_non_nda_sites')) {
    function get_non_nda_sites()
    {
        $data = Sites::where('onboard_nda',0)->get()->sortBy('site_name');
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }
}

if(!function_exists('get_fy_month')) {
    function get_fy_month($fromYear,$endYear)
    {
        // Get year and month of initial date (From)
        $yearIni = date("Y", strtotime($fromYear));
        $monthIni = date("m", strtotime($fromYear));

        // Get year an month of finish date (To)
        $yearFin = date("Y", strtotime($endYear));
        $monthFin = date("m", strtotime($endYear));

        if ($yearIni == $yearFin) {
            return $numberOfMonths = ($monthFin-$monthIni) + 1;
        } else {
            if($yearFin == date('Y')) {
                return $numberOfMonths = ((($yearFin - $yearIni) * 12) - $monthIni) + 1 + $monthFin;
            }
            else {
                return $numberOfMonths = (date('m')-$monthIni) + 1;
            }
        }
    }
}

if(!function_exists('getSiteAccValidation')){
    function getSiteAccValidation($site_id)
    {
        $data = Sites::select('ac_validation')->where('id',$site_id)->first();

        if(isset($data)){
            return $data;
        } else{ return null; }
    }
}

if (!function_exists('get_gateway_name')) {
    function get_gateway_name($domain)
    {
        $result  = DB::table('sites')->select('id')->where('site_domain', $domain)->first();
        $site_id = $result->id;
        // dd($site_id);

        $getGateway = DB::table('site_gateways')->select('payment_gateways.name as gateway_name')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 0)->first();
        if (isset($getGateway->gateway_name)) {
            return $getGateway->gateway_name;
        } else {
            return null;
        }
    }
}

if (!function_exists('getFirstChar')) {
    function getFirstChar($string)
    {
        $words = explode(' ', $string);
        if(empty($words[1])){
           return strtoupper(substr($words[0], 0, 2));
        } else {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }
    }
}

if(!function_exists('get_deal_tenant_users')) {
    function get_deal_tenant_users($deal_id){
        $data = DB::table('deal_user')->leftjoin('users','users.id','deal_user.user_id')->select('users.id','users.name')->where('deal_user.deal_id',$deal_id)->where('deal_user.user_type',2)->get();
        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_user_type1_deal_users')) {
    function get_user_type1_deal_users($dealid)
    {
        $user = DB::table('deal_user')
            ->leftJoin('users', 'deal_user.user_id', '=', 'users.id')
            ->select('users.name as name', 'users.id', 'deal_user.pivot_field1', 'deal_user.pivot_field2')
            ->where('deal_user.deal_id', $dealid)
            ->where('deal_user.user_type', 1)
            ->get();
        if ($user) {
            return $user;
        }
    }
}

if(!function_exists('get_esign_service_type')){
    function get_esign_service_type($site_id){
        $type = Sites::select('esign_service')->where('id',$site_id)->first();

        if(isset($type)){
            return $type;
        }
    }
}

if(!function_exists('get_input_fields')){
    function get_input_fields($site_id){
        $fields = SubDealTypeFieldMapping::select('fields')->where('site_id',$site_id)->first();

        if(isset($fields)){
            return $fields;
        }
    }
}

/*** AXIS BANK ENCRYPTION-DECRYPTION CODE STARTS ****/
if (!function_exists('encrypt_bank')) {
    function encrypt_bank($plaintext, $password) {
        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv); //16

        $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true); //32
        $res = base64_encode($iv . $hash . $ciphertext);
        \Log::info("RESULT ENCRYPT(encrypt_bank) ==> ".json_encode($res)."\n");
        return $res;
    }
}

if (!function_exists('decrypt_bank')) {
    function decrypt_bank($ivHashCiphertext, $password) {
        $ivHashCiphertext = base64_decode($ivHashCiphertext);
        $method = "AES-256-CBC";

        \Log::info("DECRYPT LENGH ivHashCiphertext : ".strlen($ivHashCiphertext));
        $iv = substr($ivHashCiphertext, 0, 16);
        \Log::info("DECRYPT LENGH iv : ".strlen($iv));
        $hash = substr($ivHashCiphertext, 16, 32);
        \Log::info("DECRYPT LENGH hash : ".strlen($hash));
        $ciphertext = substr($ivHashCiphertext, 48);
        \Log::info("DECRYPT LENGH ciphertext : ".strlen($ciphertext));

        $key = hash('sha256', $password, true);
        $res = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
        \Log::info("DECRYPTED ciphertext : ".$res);

        if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) return null;

        //return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $res = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
        \Log::info("RESULT DECRYPT(decrypt_bank) ==> ".json_encode($res));
        return $res;
    }
}

if (!function_exists('generateEncryptedStringForAxisApi')) {
    function generateEncryptedStringForAxisApi($array, $parentKeys = []) {
        $result = [];
        $newString = "";
        foreach ($array as $key => $value) {
        $currentKeys = array_merge($parentKeys, [$key]);

        if (is_array($value)) {
        $nestedResult = extractKeyValues($value, $currentKeys);
        $result = array_merge($result, $nestedResult);
        } else {
        $result[implode('+', $currentKeys)] = $value;
        }
        }

        foreach ($result as $key => $value) {
        $newString .= "+".$value;
        }
        return $result;
    }
}
/*** AXIS BANK ENCRYPTION-DECRYPTION CODE ENDS ****/

if (!function_exists('getSiteBank')) {
    function getSiteBank($site_id)
    {
        $gateway_code = DB::table('site_gateways')->select('payment_gateways.gateway_code as gateway_code')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 1)->first();
        if (isset($gateway_code)) {
            return $gateway_code;
        } else {
            return null;
        }
    }
}

if (!function_exists('externalSiteNachIdGenerator')) {
    function externalSiteNachIdGenerator($idtype, $pid,$site_code)
    {
        $site_code = env('APP_ENV', 'local') == 'prod' ? $site_code : $site_code . 'UAT';

        $type = 0;
        $id   = 1;
        if ($idtype == 0 || $idtype == 1) {
            // CONSUMER ID & MANDATE REGISTRATION TXN ID
            $type   = $idtype;
            $lastId = DB::table('enach_registration')->orderby('id', 'desc')->first();
            $id += isset($lastId) ? $lastId->id : 0;
        } else if ($idtype == 2) {
            // TXN SCHEDULING TXN ID
            $type   = '2';
            $lastId = DB::table('enach_txn_scheduling')->orderby('id', 'desc')->first();
            $id += isset($lastId) ? $lastId->id : 0;
        }

        return $site_code . $type . '0' . $pid . '0' . $id;
    }
}

function webhookCallNew($data)
{
    try{
        \Log::info("Webhook Payload Received with payload : ".json_encode($data));

        $site_id = isset($data['site_id']) ? $data['site_id'] : epcache('site_id');
        $site = DB::table('sites')->where('id', $site_id)->first();

        if (array_key_exists('site_id', $data)) {
            unset($data['site_id']);
        }

        if(isset($site->pg_webhook)){
            $headerKey = "apiKey";
            $headerValue = "";
            if(isset($site->pg_token)){
                $webhookHeader = json_decode($site->pg_token,true);
                $headerKey = array_keys($webhookHeader)[0];
                $headerValue = array_values($webhookHeader)[0];
            }
            WebhookCall::create()
                ->doNotSign()
                ->withHeaders([$headerKey => $headerValue])
                ->url($site->pg_webhook)
                ->payload($data)
                ->dispatch();
        } else {
            \Log::info("Webhook Payload is not configured for this site : ".$site_id);
        }
    } catch (\Exception $ex){
        \Log::error("Exception occured while calling new webhook call: ".$ex->getMessage());
    }
}


if (!function_exists('getSiteGatewayId')) {
    function getSiteGatewayId($site_id){
        $siteGateway = DB::table('site_gateways')->select('payment_gateways.id as id')->leftjoin('payment_gateways', 'site_gateways.paymentgatyway_id', 'payment_gateways.id')->where('site_gateways.sites_id', $site_id)->where('payment_gateways.type', 1)->first();
        return $siteGateway->id;
    }
}

if (!function_exists('checkUserDealMapping')) {
    function checkUserDealMapping($dealid,$userid){
        return DB::table('deal_user')->where('deal_id', $dealid)->where('user_id', $userid)->exists();
    }
}

if (!function_exists('getAgrmntVerifyStatus')) {
    function getAgrmntVerifyStatus($siteId) {
        $siteOnboardStatus = DB::table('sites')->where('id',$siteId)->first();
        $agreement = DB::table('agreement_signings')->leftjoin('agreements','agreements.id','agreement_signings.agreement_id')->where('agreements.site_id',$siteId)->where('agreement_signings.status', 2)->where('agreement_signings.etype','authorisation')->orderby('agreements.id','desc')->first();
        if (isset($agreement)) {
            return '<em class="icon ni ni-check-circle" style="color:green; font-size:18px;"></em>';
        } elseif(isset($siteOnboardStatus) && $siteOnboardStatus->onboard_nda == 2) {
            return '<a href="#"  data-siteid="'.$siteId.'" class="btn btn-success approveAgrmntBtn" data-toggle="modal" data-target="#approveAgrmntModal">Verify & Upload</a>';
        } else {
            return '';
        }
    }
}

if (!function_exists('calculateInclusiveAmount')) {
    function calculateInclusiveAmount($exclusiveAmount) {
        $gstRate = 18; // GST rate in percentage
        $gstRateDecimal = $gstRate / 100;
        $gstValue = $exclusiveAmount * $gstRateDecimal;
        $inclusiveAmount = $exclusiveAmount + $gstValue;

        return [
            'inclusiveAmount' => $inclusiveAmount,
            'gstValue' => $gstValue,
        ];
    }
}

if(!function_exists('onboardStatus')) {
    function onboardStatus() {
        $status = Sites::where('id',epcache('site_id'))->first();
        if($status) {
            return $status->onboard_nda;
        }
    }
}

if(!function_exists('getOnboardStatus')) {
    function getOnboardStatus($siteId) {
        $site = Sites::select('onboard_nda')->where('id',$siteId)->first();
        $status = $site->onboard_nda;
        $onboardStatus = '';
        switch ($status) {
            case 0:
                $onboardStatus = "NEW";
                break;
            case 1:
                $onboardStatus = "KYC SUBMITTED";
                break;
            case 2:
                $onboardStatus = "LOA SIGNED";
                break;
            case 3:
                $onboardStatus = "KYC VERIFIED";
                break;
            case 4:
                $onboardStatus = "AGREEMENT SIGNED";
                break;
            case 5:
                $onboardStatus = "FEE PAID";
                break;
            case 6:
                $onboardStatus = "UAT";
                break;
            case 7:
                $onboardStatus = "LIVE";
                break;
        }
        return $onboardStatus;
    }
}

if(!function_exists('nameOfEntityType')) {
    function nameOfEntityType($type) {
        \Log::info("Type :: ".$type);
        $description = '';
        switch($type) {
            case 'Pvt Co':
                $description = 'Private Limited Company';
                break;
            case 'Pbt Co':
                $description = 'Public Limited Company';
                break;
            case 'Prt Co':
                $description = 'Partnerships Company';
                break;
            case 'LLP':
                $description = 'Limited Liability Partnership (LLP)';
                break;
            case 'OP Co':
                $description = 'One Person Company';
                break;
            case 'SP':
                $description = 'Sole Proprietorship';
                break;
            case 'NGO':
                $description = 'Section 8 Company (NGO)';
                break;
            case 'Other':
                $description = 'Other';
                break;
            default:
                $description = 'Please Select';
                break;
        }
        return $description;
    }
}
 

if(!function_exists('randomStrings')) {
    function randomStrings($length_of_string) {

    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 0, $length_of_string);
}

}



if (!function_exists('get_loggedin_user_siteid')) {
    function get_loggedin_user_siteid()
    {
        $res= DB::table('site_users')->where('user_id',Auth::user()->id)->first();
        if(!empty($res)){
            return $res->site_id;
        }else{
            return 0;
        }
    }
}