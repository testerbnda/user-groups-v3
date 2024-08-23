<?php

namespace Modules\Core\Helpers;

use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\Logger;
use Modules\Core\Jobs\SendEmailJob;
use Modules\Escrow\Entities\Escrow;
use Modules\Escrow\Entities\Transactions;
use Modules\Escrow\Entities\TransactionFlows;
use Modules\Manager\Services\ManagerService;
use Modules\Deal\Services\DealService;
use Modules\User\Entities\Sites;
use Modules\Deal\Entities\Deal;
use Modules\Deal\Entities\Dealdoc;
use Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Consent;
use App\Models\Agreement;
use App\Models\AgreementSignings;
use Modules\Core\Services\MailSentService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Mail;
use Modules\Core\Helpers\BasicHelper;
use Modules\Paymentgatyway\Repositories\Implementations\AxisBankRepository;
use App\Models\Userbank;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKey;

class BasicHelper
{

    /**
     * Display date in d m y format
     * @param datetime $date
     * @return string
     */
    public static function showDate($date = '0', $format = 'j M Y')
    {
        //Get Data from object
        if (is_object($date)) {
            $date = (string) $date;
        }

        //changing date format and return 0 incase og wrong date
        if (strtotime($date) && $date != 0) {
            $date = date($format, strtotime($date));
        } else {
            $date = 'N/A';
        }

        return $date;
    }

    /**
     * Function to trim description
     * @param type $string
     * @return type
     */
    public static function showDescription($string)
    {
        $text = "";
        if (strlen($string) > 200) {
            $text = substr(strip_tags($string, '<img>'), 0, 200) . "...";
        } else {
            $text = strip_tags($string, '<img>');
        }
        return html_entity_decode($text);
    }

    /**
     * Check if passed value is an instance of Exception
     * @param Exception $value
     * @return type
     */
    public static function isException($value)
    {
        return ($value instanceof Exception) ? true : false;
    }

    public static function showAttatchment(Attachment $attachment, $height = 50, $width = 50)
    {
        return !empty($attachment->path) ? config('attachment.image_cdn') . 'tr:w-' . $height . ',h-' . $width . '/' . $attachment->path : null;
    }

    /**
     * Make a cURL HTTP Request
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @param boolean $jsonDecode
     * @return boolean
     */
    public static function httpCurlRequest(string $endpoint, string $method = "GET", array $data = array(), bool $jsonDecode = false)
    {
        try {
            $client   = new \GuzzleHttp\Client();
            $response = $client->request($method, $endpoint, ['query' => $data]);
            return ($jsonDecode) ? json_decode($response->getBody()->getContents()) : $response->getBody()->getContents();
        } catch (Exception $ex) {
            Logger::error($ex);
            return false;
        }
    }

    /**
     * Get products from Nykaa
     * @param type $q
     * @return array
     */
    public static function products($q)
    {
        $contents            = self::httpCurlRequest(config('admin.product_suggestion'), "GET", ["q" => $q], true);
        $products["results"] = array();
        if ($contents->status == "OK" && !empty($contents->suggestions)) {
            foreach ($contents->suggestions as $suggestion) {
                if ($suggestion->type == "product") {
                    array_push($products["results"], [
                        "id"       => $suggestion->id,
                        "text"     => $suggestion->q,
                        "image"    => $suggestion->image,
                        "imageurl" => $suggestion->image_base,
                        "url"      => $suggestion->url,
                    ]);
                }
            }
        }
        return $products;
    }

    /**
     * Compare 2 dates
     * @param type $date1
     * @param type $date2
     * @return bool
     */
    public static function compareDateTime($date1, $date2)
    {
        $dateTimestamp1 = strtotime(self::formatDateAndTimePicker($date1));
        $dateTimestamp2 = strtotime($date2);
        // Compare the timestamp date
        return ($dateTimestamp1 >= $dateTimestamp2) ? true : false;
    }

    /**
     * FUnction to set emails for notified users
     */
    public static function setNotifiedUserEmail($notificationEmail)
    {
        if (!empty(config('mail.debug')) && !empty(config('mail.debug_emails'))) {
            $notificationEmail = config('mail.debug_emails');
        }
        return $notificationEmail;
    }

    public static function formatEncryptData($string)
    {
        $encryptedData = Crypt::encryptString($string);
        return $encryptedData;
    }

    public static function formatDecryptData($string)
    {
        $decryptedData = Crypt::decryptString($string);
        return $decryptedData;
    }

    // This function will return a random
    // string of specified length
    public static function randomStrings($length_of_string)
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shuffle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }

    public static function get_site_id()
    {
        $domain  = $_SERVER['HTTP_HOST'];
        $result  = Sites::select('id')->where('site_domain', $domain)->first();
        $site_id = $result->id;

        return $site_id;
    }

    public static function numberToCurrency($number)
    {
        $decimal   = (string) ($number - floor($number));
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
            $result = $result . $decimal;
        }
        return '₹ ' . $result;
    }

    public static function getEscrowBalance()
    {
        $user = \Auth::user();
        if ($user) {
            if (is_admin() || is_manager()) {
                $site_id = epcache('site_id');
                $user_id = $site_id;
                $ac_type = -1;
            } else {
                $user_id = $user->id;
                $ac_type = 0;
            }
            $escrowbalance = Escrow::where('user_id', $user_id)->where('ac_type', $ac_type)->first();
            if ($escrowbalance != null) {
                return BasicHelper::numberToCurrency($escrowbalance->balance);
            } else {
                return BasicHelper::numberToCurrency('0');
            }
        } else {
            return BasicHelper::numberToCurrency('0');
        }
    }

    public static function getStringFirstChar($string)
    {
        $string = $string != '' ? $string[0] : '';
        return ucfirst($string);
    }

    public static function randomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public static function uploadFileToDisk($disk, UploadedFile $file, $filename, $mimeType)
    {
        $envData     = env('APP_ENV', 'local');
        $docFolder   = $envData == 'prod' ? 'centralservices' : 'centraluat';
        $mimeTypeArr = array();
        if ($mimeType != '') {
            $mimeTypeArr = ['visibility' => 'public', 'mimetype' => $mimeType];
        }
        $filePath = $docFolder . '-documents/' . $filename;
        Storage::disk('s3')->put($filePath, file_get_contents($file), $mimeTypeArr);
        return $s3filepath = 'https://d1tq5769y0bfry.cloudfront.net/' . $docFolder . '-documents/' . urlencode($filename);
    }

    public static function put_file_contents($file_name, $content)
    {
        $envData   = env('APP_ENV', 'local');
        $docFolder = $envData == 'prod' ? 'centralservices' : 'centraluat';

        $filePath = $docFolder . '-documents/' . $file_name;
        Storage::disk('s3')->put($filePath, $content);
        return $s3filepath = 'https://d1tq5769y0bfry.cloudfront.net/' . $docFolder . '-documents/' . urlencode($file_name);
    }

    public static function generateEscrowAcNo($siteid, $user, $userpan, $actype, $payinsrc, $created_at,$acNo = null)
    {
        $getBankCode = get_bank_code($siteid);
        if($getBankCode == 'axis-bank'){
            $corpCode = env('AXIS_AC_CORP_CODE','ABCD');
            $bank_name = 'AXIS';
            $ifsc_code = 'UTIB0CCH274';
        } elseif($getBankCode == 'indusind-bank'){
            $corpCode = env('IBL_AC_CORP_CODE','ZDREAM');
            $bank_name = 'Indusind';
            $ifsc_code = 'INDB0000001'; //@TODO
        } else {
            $corpCode = 'EPUG';
            $bank_name = 'ICICI';
            $ifsc_code = 'ICIC0000104';
        }
        $sitecode  = Sites::where('id', $siteid)->first()->site_code;
        $accountno = isset($acNo) ? strtoupper($corpCode . $acNo) :strtoupper($corpCode . $sitecode . $userpan . (($payinsrc == '') ? '' : $payinsrc));

        DB::table('virtual_escrow_accounts')->insert([
            'site_id'      => $siteid,
            'user_id'      => $user,
            'escrow_ac_no' => $accountno,
            'ac_type'      => $actype,
            'payin_src'    => ($payinsrc == '') ? null : $payinsrc,
            'balance'      => 0,
            'bank_name'    => $bank_name,
            'ifsc_code'    => $ifsc_code,
            'created_at'   => $created_at == 0 ? date('Y-m-d H:i:s') : $created_at,
            'status'       => 0,
        ]);
    }

    public function escrowIncrementAmount($amount, $account_id)
    {
        $res = Escrow::where('id', $account_id)->increment('balance', $amount);
        if ($res) {
            $escrow = Escrow::where('id', $account_id)->first('balance');
            return $escrow->balance;
        } else {
            return 0;
        }
    }

    public function escrowDecrementAmount($amount, $account_id)
    {
        $res    = Escrow::where('id', $account_id)->where('balance', '>=', $amount)->decrement('balance', $amount);
        $escrow = Escrow::where('id', $account_id)->first('balance');
        if ($res) {
            return $escrow->balance;
        } else {
            return 0;
        }
    }

    public function release_funds($site_id, $amount, $from_ac, $to_ac, $from_par, $to_par, $bank_id)
    {
        try {
            $txnDr = BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $from_par, $amount, $from_ac, $to_ac, null, 1, null);
            $txnCr = BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $to_par, $amount, $to_ac, $from_ac, null, 1, null);

            $payout_id = DB::table('site_gateways')->leftjoin('payment_gateways','site_gateways.paymentgatyway_id','payment_gateways.id')->select('payment_gateways.id as pg_id','payment_gateways.gateway_code')->where('payment_gateways.type', 1)->where('site_gateways.sites_id', $site_id)->first()->pg_id;
            $txnBk = BasicHelper::escrow_account_transaction($site_id, 'debit', 0, 'Withdrawal Request', $amount, $to_ac, null, $bank_id, 0, null, $payout_id);
            return [$txnDr, $txnCr, $txnBk];
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Funds Released failed Error=".$ex->getMessage());
            return null;
        }
    }

    public function fund_transfer($site_id, $amount, $from_ac, $to_ac, $from_par, $to_par)
    {
        try {
            $txnDr = BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $from_par, $amount, $from_ac, $to_ac, null, 1, null);
            $txnCr = BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $to_par, $amount, $to_ac, $from_ac, null, 1, null);

            //auto-withdrawal code
            $siteInfo = Sites::where('id', $site_id)->first();
            if ($siteInfo->auto_withdraw == 1) {
                BasicHelper::withdrawal_request($site_id, $amount, $to_ac);
            }
            return [$txnDr, $txnCr];
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Funds Released failed Error=".$ex->getMessage());
            return null;
        }
    }

    public function refund_transaction($txn_id, $particular)
    {
        try{
            $pTxn = Transactions::where('id', $txn_id)->first();
            $pTxn->update(['status' => 7, 'trustee_rej_reason' => $particular]);
            BasicHelper::escrow_account_transaction($pTxn->site_id, 'credit', 3, "Refund - Payout Failed to Proccess", $pTxn->amount, $pTxn->account_id, null, null, 1, null, null, null, $txn_id, null);
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Refund Transaction Failed with Error=".$ex->getMessage());
            return false;
        }
    }

    public function send_email($site_id, $template_code, $emaildata, $email_to, $email_cc, $email_bcc)
    {
        try {
            $siteData = Sites::where('id', $site_id)->first();

            if ($siteData && $siteData->email_service == 1) {
                 $templateinfo = DB::table('site_notifications')->select('templates.id','site_notifications.status')->leftjoin('templates', 'site_notifications.template_id', 'templates.id')->where('templates.template_code', $template_code)->where('site_notifications.status', 1)->where('site_notifications.site_id', $site_id)->orderBy('templates.temp_level','desc')->first();
                if (isset($templateinfo) && $templateinfo->status == 1) {
                    $emaildata['site_name']     = isset($emaildata['site_name']) ? $emaildata['site_name'] : $siteData->site_name;
                    $emaildata['template_id'] = $templateinfo->id;
                    $emaildata['email_to']      = $email_to;
                    $emaildata['email_cc']      = $email_cc;
                    $emaildata['email_bcc']     = $email_bcc;
                    dispatch(new SendEmailJob($emaildata));
                }
            }
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Sending mail with Error=".$ex->getMessage());
        }
    }

    public function custom_send_email(Request $request)
    {

        try{
        $data = $request->only('site_id', 'template_code', 'email_data', 'email_to', 'email_cc', 'email_bcc','email_from','email_alias');

        $email_data['name'] = $data['email_data']['name'];
        Mail::send('layouts.customleadsemail', ['name' => $email_data['name']], function ($message) use ($data) {
            $message->from($data['email_from'], $data['email_alias'])
            ->to($data['email_to'])
            ->subject('Greetings from Escrowpay - India\'s First Digital Escrow Platform');
        });
        return response()->json(['success' => true, 'message' => 'Mail has been sent'],200);


    }
    catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()],400);
    }
    }

    /** Auto-withdrawal Request Code */
    public function withdrawal_request($site_id, $amount, $account_id)
    {
        try {
            $escrow = Escrow::where('id', $account_id)->first();
            \Log::info($_SERVER['HTTP_HOST'].": Auto-Withdrawal Request received for site_id=".$site_id.",amount=".$amount.", account_id=".$account_id.", balance=".$escrow->balance);
            $envData = env('APP_ENV', 'local');
            $sites = Sites::select('site_code')->where('id',$escrow->site_id)->first();
            if(isset($sites) && $sites->site_code != 'TRUST'){
                if ($escrow->ac_type == -1) {
                    $user_name = Sites::where('id', $escrow->user_id)->first()->site_name;
                    $bank      = DB::table('userbanks')->Join('model_has_roles', 'model_has_roles.model_id', '=', 'userbanks.user_id')
                        ->Join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->select('userbanks.*')
                        ->where('roles.site_id', $escrow->site_id)
                        ->where('roles.name', 'admin')
                        ->where('userbanks.bank_status', 1)
                        ->first();
                } elseif ($escrow->ac_type != 1 && $escrow->ac_type != 2 && $escrow->ac_type != 3 && $escrow->ac_type != 4) {
                    $user_name = User::where('id', $escrow->user_id)->first()->name;
                    $bank      = DB::table('userbanks')->where('user_id', $escrow->user_id)->where('bank_status', 1)->first();
                }

                if (isset($bank)) {
                    $amount = ($escrow->balance > $amount ) ? $amount : $escrow->balance;
                    if ($amount > 0) {
                        \Log::info($_SERVER['HTTP_HOST'].": Auto-Withdrawal Request processed for site_id=".$site_id.",amount=".$amount.", account_id=".$account_id.", balance=".$escrow->balance);

                        $payout_id = null;
                        $pginfo = DB::table('site_gateways')->leftjoin('payment_gateways','site_gateways.paymentgatyway_id','payment_gateways.id')->select('payment_gateways.id as pg_id','payment_gateways.gateway_code')->where('payment_gateways.type', 1)->where('site_gateways.sites_id', $site_id)->first();
                        if (isset($pginfo)) {
                            $payout_id = $pginfo->pg_id;
                        }
                        if ($envData == 'prod') {
                            if($pginfo->gateway_code == 'axis-bank' && $bank->vendor == 0 && $bank->bank_code == null){
                                $data['bank_acc'] = $bank->bank_account;
                                $data['user_name'] = $bank->name;
                                $data['bank_name'] = $bank->bank_name;
                                $data['ifsc_code'] = $bank->bank_ifsc;
                                $data['user_id'] = $escrow->user_id;
                                $bankInfo = AxisBankRepository::axisBeneRegistration($data);

                                $oldBankResponse = $bank->bank_response_data;
                                $appendOldNewResponse = [$oldBankResponse,$bankInfo['response_data']];

                                UserBank::where('id',$bank->id)->update([
                                    'bank_code' => $bankInfo['bene_code'],
                                    'vendor' => $bankInfo['vendor'],
                                    'bank_response_data' => $appendOldNewResponse,
                                    'updated_at' => date("Y-m-d H:i:s")
                                ]);
                            }
                        }
                        $transactions = BasicHelper::escrow_account_transaction($site_id, 'debit', 0, 'Withdrawal Request', $amount, $account_id, null, $bank->id, 0, null, $payout_id);

                        // deduct EP Fee
                        $epfee_res = BasicHelper::ep_fee_transaction($site_id, 'payout', 'EP Fee - Pay Out #'.$transactions->id, $amount, null, $transactions->id);

                        $bank_acc = DB::table('userbanks')->where('id', $transactions->bank_id)->first();

                        $emaildata = array('txn_id' => $transactions->id, 'user_name' => $user_name, 'amount' => numberToCurrency($amount),'bank_acc_no' => $bank_acc->bank_account, 'ifsc_code' => $bank_acc->bank_ifsc);
                        BasicHelper::send_email($site_id, 'withdrawal_request', $emaildata, config('mail.TRUSTEE_MAIL'), null, config('mail.ADMINS'));
                    }
                } else {
                    \Log::info($_SERVER['HTTP_HOST'].": Bank Account details not found for user_id=".$escrow->user_id);
                }
            } else {
                \Log::info($_SERVER['HTTP_HOST'].": Auto-Withdrawal feature is off for site_id=".$escrow->site_id." and site_code =".$sites->site_code);
            }
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Auto-Withdrawal Request failed for account_id=".$account_id.", Error=".$ex->getMessage());
        }
    }

/**
 * Transaction with row level locking.
 * @param array $site_id,$type,$txn_type,$particular,$amount,$account_id,$party_ac_id,$bankid,$status
 * @return Response
 */
    public static function escrow_account_transaction($site_id, $type, $txn_type, $particular, $amount, $account_id, $party_ac_id, $bankid, $status, $utr_no, $payout_id = null, $party_name = null, $ptxn_id = null, $no_of_days = null)
    {
        $escrowAccount = Escrow::where('id', $account_id)->lockForUpdate()->first();
        $balance = $escrowAccount->balance;
        if ($type == 'debit') {
            $currBalance = $balance - $amount;
            if ($currBalance < 0 && $escrowAccount->ac_type > -2) {
                throw new \Exception('Insufficient balance to process payment.');
            }
        } else {
            $currBalance = $balance + $amount;
        }
        Escrow::where('id', $account_id)->update(['balance' => $currBalance]);

        $tdata['bank_id']     = $bankid;
        $tdata['amount']      = $amount;
        $tdata['balance']     = $currBalance;
        $tdata['site_id']     = $site_id;
        $tdata['particular']  = $particular;
        $tdata['utr_no']      = $utr_no;
        $tdata['type']        = $type;
        $tdata['txn_type']    = $txn_type;
        $tdata['ptxn_id']     = $ptxn_id;
        $tdata['txn_date']    = date('Y-m-d H:i:s');
        $tdata['party_ac_id'] = $party_ac_id;
        $tdata['account_id']  = $account_id;
        $tdata['status']      = $status;
        $tdata['payout_id']   = $payout_id;
        $tdata['party_name']   = $party_name;
        $tdata['no_of_days']   = $no_of_days;

        return Transactions::create($tdata);
    }

    public static function escrow_account_due_transaction($site_id, $type, $txn_type, $particular, $amount, $account_id, $party_ac_id, $bankid, $status, $utr_no, $payout_id = null, $party_name = null)
    {
        $balance = Escrow::where('id', $account_id)->lockForUpdate()->first()->balance;
        if ($type == 'debit') {
            if ($amount <= $balance) {
                $dueAmount = 0;
                $currBalance = $balance - $amount;
                $amount = $amount;
            } else {
                $dueAmount = $amount - $balance;
                $currBalance = 0;
                $amount = $balance;
            }
        } else {
            $dueAmount = 0;
            $currBalance = $balance + $amount;
            $lendorDueAMt = Escrow::where('id', $party_ac_id)->lockForUpdate()->first()->due_amount;
            $amount = $amount - $lendorDueAMt;
        }

        Escrow::where('id', $account_id)->update(['balance' => $currBalance, 'due_amount' => $dueAmount]);

        $tdata['bank_id']     = $bankid;
        $tdata['amount']      = $amount;
        $tdata['balance']     = $currBalance;
        $tdata['site_id']     = $site_id;
        $tdata['particular']  = $particular;
        $tdata['utr_no']      = $utr_no;
        $tdata['type']        = $type;
        $tdata['txn_type']    = $txn_type;
        $tdata['txn_date']    = date('Y-m-d H:i:s');
        $tdata['party_ac_id'] = $party_ac_id;
        $tdata['account_id']  = $account_id;
        $tdata['status']      = $status;
        $tdata['payout_id']   = $payout_id;
        $tdata['party_name']   = $party_name;

        return Transactions::create($tdata);
    }


/**
 * Clearing Transaction with row level locking.
 * @param array $site_id,$type,$txn_type,$particular,$amount,$account_id,$party_ac_id,$bankid,$status
 * @return Response
 */
  public static function escrow_account_clearing_transaction($site_id, $txnid,$type, $txn_type, $particular, $amount, $account_id, $party_ac_id, $bankid, $status, $utr_no, $payout_id = null,$payment_type,$isAmount,$tbl)
    {
        \Log::info($_SERVER['HTTP_HOST'].": payment_type  >> ".json_encode($payment_type));
        if($tbl==0){

        if($isAmount==0){
        $balance = Escrow::where('id', $account_id)->lockForUpdate()->first()->balance;
        } else {
        $balance = Escrow::where('id', $account_id)->lockForUpdate()->first()->clearing_amount;
        }
        if ($type == 'debit') {
            $currBalance = $balance - $amount;
        } else {
            $currBalance = $balance + $amount;
        }

        if($isAmount==0){
        Escrow::where('id', $account_id)->update(['balance' => $currBalance]);
        \Log::info($_SERVER['HTTP_HOST'].": balance update  >> ".$account_id.json_encode($currBalance));
        } else {
        Escrow::where('id', $account_id)->update(['clearing_amount' => $currBalance]);
        \Log::info($_SERVER['HTTP_HOST'].": clearing_amount update >> ".$account_id.json_encode($currBalance));
        }

    }


        if($isAmount==0 || $tbl==0){
        $tdata['bank_id']     = $bankid;
        $tdata['amount']      = $amount;
        $tdata['balance']     = $currBalance;
        $tdata['site_id']     = $site_id;
        $tdata['particular']  = $particular;
        $tdata['utr_no']      = $utr_no;
        $tdata['type']        = $type;
        $tdata['txn_type']    = $txn_type;
        $tdata['txn_date']    = date('Y-m-d H:i:s');
        $tdata['party_ac_id'] = $party_ac_id;
        $tdata['account_id']  = $account_id;
        $tdata['status']      = $status;
        $tdata['payout_id']   = $payout_id;
        return Transactions::create($tdata);
        } else {
        $tdata['txn_id']     = $txnid;
        $tdata['amount']     = $amount;
        $tdata['site_id']     = $site_id;
        $tdata['particular']  = $particular;
        $tdata['utr_no']      = $utr_no;
        $tdata['txn_date']    = date('Y-m-d H:i:s');
        $tdata['to_ac_id'] = $party_ac_id;
        $tdata['from_ac_id']  = $account_id;
        $tdata['to_bank_id']  = $bankid;
        $tdata['status']      = $status;
        $tdata['payment_type']      = $payment_type;

         return TransactionFlows::create($tdata);
        }
    }



    /**
     * Get Bank name from IFSC CODE
     */
    public function get_bank_name($ifsc_code)
    {
        $code = strtoupper(substr($ifsc_code, 0, 4));

        $bank         = array();
        $bank['UTIB'] = 'Axis Bank';
        $bank['BKID'] = 'Bank of India';
        $bank['CNRB'] = 'Canara Bank';
        $bank['HDFC'] = 'HDFC Bank';
        $bank['ICIC'] = 'ICICI Bank';
        $bank['KKBK'] = 'Kotak Mahindra Bank';
        $bank['PUNB'] = 'Punjab National Bank';
        $bank['ABHY'] = 'Abhyudaya Co-op Bank Ltd';
        $bank['ALLA'] = 'Allahabad Bank';
        $bank['ANDB'] = 'Andhra Bank';
        $bank['BARB'] = 'Bank of Baroda';
        $bank['MAHB'] = 'Bank Of Maharashtra';
        $bank['CBIN'] = 'Central Bank of India';
        $bank['CITI'] = 'Citibank India';
        $bank['BKDN'] = 'Dena Bank';
        $bank['HSBC'] = 'HSBC';
        $bank['YESB'] = 'Yes Bank';
        $bank['AMCB'] = 'Ahmedabad Mercantile Co-op Bank';
        $bank['AIRP'] = 'Airtel Payments Bank Ltd';
        $bank['AKJB'] = 'Akola Janta Commerical Cooperative Bank';
        $bank['CITI'] = 'Almora Urban Co-op Bank Ltd';
        $bank['AUBL'] = 'Au Small Finance Bank';
        $bank['ANZB'] = 'Australia and New Zealand Banking Group Limited';

        $bank['BNPA'] = 'BNP Paribas';
        $bank['BDBL'] = 'Bandhan Bank';
        $bank['BOFA'] = 'Bank Of America';
        $bank['BBKM'] = 'Bank Of Bahrain And Kuwait';
        $bank['BCEY'] = 'Bank Of Ceylon';
        $bank['BARC'] = 'Barclays Bank Plc';
        $bank['BACB'] = 'Bassein Catholic Co-op Bank Ltd';
        $bank['BNSB'] = 'Bhagini Nivedita Sahakari Bank Pune';
        $bank['BCBM'] = 'Bharat Co-op Bank (Mumbai) Ltd';
        $bank['BMCB'] = 'Bombay Mercantile Cooperative Bank';

        $bank['CLBL'] = 'Capital Small Finance Bank';
        $bank['CCBL'] = 'Citizen Credit Cooperative Bank';
        $bank['CIUB'] = 'CityUnion Bank Ltd';
        $bank['COAS'] = 'Coastal Local Area Bank';
        $bank['CRLY'] = 'Credit Agricole Corp and Investment Bank';
        $bank['CRES'] = 'Credit Suisse Ag';
        $bank['CSBK'] = 'Catholic Syrian Bank Ltd';

        $bank['HDFC'] = 'Darussalam Cooperative Urban Bank';
        $bank['DBSS'] = 'DBS Bank';
        $bank['DCBL'] = 'Dcb Bank';
        $bank['DEOB'] = 'Deogiri Nagari Sahakari Bank Aurangabad';
        $bank['DICG'] = 'Deposit Insurance And Credit Guarantee Corporation';
        $bank['DEUT'] = 'Deutsche Bank';
        $bank['DLXB'] = 'Dhanlaxmi Bank Ltd';
        $bank['DMKJ'] = 'Dmk Jaoli Bank';
        $bank['DOHB'] = 'Doha Bank';
        $bank['DNSB'] = 'Dombivli Nagari Sahakari Bank Ltd';
        $bank['DURG'] = 'Durgapur Steel Peoples Cooperative Bank';

        $bank['EBIL'] = 'Emirates Nbd Bank';
        $bank['ESFB'] = 'Equitas Small Finance Bank';
        $bank['EIBI'] = 'Export-Import Bank of India';

        $bank['FDRL'] = 'Federal Bank Ltd';
        $bank['FSFB'] = 'Fincare Small Finance Bank';
        $bank['FINO'] = 'Fino Payments Bank';
        $bank['FIRN'] = 'Firstrand Bank Ltd';
        $bank['IBKL'] = 'Hutatma Sahakari Bank';

        $bank['IBKL'] = 'IDBI Bank';
        $bank['IDFB'] = 'IDFC Bank Limited';
        $bank['IDUK'] = 'Idukki District Cooperative Bank';
        $bank['IPOS'] = 'India Post Payments Bank';
        $bank['IDIB'] = 'Indian Bank';
        $bank['IOBA'] = 'Indian Overseas Bank';
        $bank['INDB'] = 'IndusInd Bank Ltd.';
        $bank['ICBK'] = 'Industrial and Commercial Bank of China Ltd';
        $bank['IBKO'] = 'Industrial Bank of Korea';

        $bank['JAKA'] = 'Jammu & Kashmir Bank Ltd.';
        $bank['JSFB'] = 'Jana Small Finance Bank';
        $bank['JSBL'] = 'Janakalyan Sahakari Bank Ltd';
        $bank['JIOP'] = 'Jio Payments Bank';
        $bank['CHAS'] = 'Jpmorgan Chase Bank';

        $bank['KCCB'] = 'Kalupur Commercial Co-op Bank Ltd';
        $bank['JSBP'] = 'Janata Sahkari Bank Ltd Pune';
        $bank['KARB'] = 'Karnataka Bank Ltd.';
        $bank['KVBL'] = 'Karur Vysya Bank';
        $bank['KOEX'] = 'KEB Hana Bank';
        $bank['KLGB'] = 'Kerala Gramin Bank';
        $bank['KBKB'] = 'Kookmin Bank';
        $bank['LAVB'] = 'Lakshmi Vilas Bank Ltd';

        $bank['MSCI'] = 'Maharashtra State Co-operative Bank Limited';
        $bank['NICB'] = 'New India Co-op Bank Ltd';
        $bank['NKGS'] = 'NKGSB Co-op Bank Ltd';
        $bank['NSPB'] = 'Nsdl Payments Bank';
        $bank['NNSB'] = 'Nutan Nagarik Sahakari Bank Ltd';
        $bank['PYTM'] = 'Paytm Payments Bank';
        $bank['PSIB'] = 'Punjab & Sind Bank';

        $bank['QNBA'] = 'Qatar National Bank SAQ';
        $bank['RABO'] = 'Rabobank International';
        $bank['RNSB'] = 'Rajkot Nagarik Sahakari Bank Ltd';
        $bank['RATN'] = 'Rbl Bank';
        $bank['RBIS'] = 'Reserve Bank Of India';

        $bank['STCB'] = 'Sbm Bank';
        $bank['SCBL'] = 'Standard Chartered Bank';
        $bank['SYNB'] = 'Syndicate Bank';
        $bank['SHBK'] = 'Shinhan Bank';
        $bank['SOGE'] = 'Societe Generale';
        $bank['SRCB'] = 'Saraswat Co-operative Bank Ltd';
        $bank['SIBL'] = 'South Indian Bank';
        $bank['SBIN'] = 'State Bank of India';
        $bank['SMBC'] = 'Sumitomo Mitsui Banking Corporation';

        $bank['TMBL'] = 'Tamilnad Mercantile Bank Ltd';
        $bank['KUCB'] = 'The Karad Urban Co-op Bank Ltd';
        $bank['UCBA'] = 'UCO Bank';
        $bank['UBIN'] = 'Union Bank of India';
        $bank['UOVB'] = 'United Overseas Bank';
        $bank['HVBK'] = 'Woori Bank';

        return array_key_exists($code, $bank) ? $bank[$code] : '';
    }

    public static function deal_escrow_account_transaction($site_id, $type, $txn_type, $particular, $amount, $account_id, $party_ac_id, $bankid, $status, $utr_no, $payout_id = null, $txnDate, $created_at)
    {
        $balance = Escrow::where('id', $account_id)->lockForUpdate()->first()->balance;
        if ($type == 'debit') {
            $currBalance = $balance - $amount;
        } else {
            $currBalance = $balance + $amount;
        }

        Escrow::where('id', $account_id)->update(['balance' => $currBalance]);

        $tdata['bank_id']     = $bankid;
        $tdata['amount']      = $amount;
        $tdata['balance']     = $currBalance;
        $tdata['site_id']     = $site_id;
        $tdata['particular']  = $particular;
        $tdata['utr_no']      = $utr_no;
        $tdata['type']        = $type;
        $tdata['txn_type']    = $txn_type;
        $tdata['txn_date']    = $txnDate;
        $tdata['party_ac_id'] = $party_ac_id;
        $tdata['account_id']  = $account_id;
        $tdata['status']      = $status;
        $tdata['payout_id']   = $payout_id;
        $tdata['created_at']  = $created_at;
        $tdata['updated_at']  = null;

        return Transactions::create($tdata);
    }

    public static function site_billing($site_id, $api_type, $entity)
    {
        $payloadUrl = url('api/v1/sitebilling/add');
        try {
            $site_plan = Db::table('billing_plans as bp')->leftjoin('site_billings as sb','bp.id','=','sb.plan_id')->select('sb.id')->where('bp.category', $api_type)->where('sb.site_id',$site_id)->where('sb.status',1)->first();
            if ($site_plan) {
                $site_plan_id = $site_plan->id;
                $ipAddress = getUserIpAddr();
                $response = Http::post($payloadUrl, ['site_plan_id' => $site_plan_id, 'entity' => $entity, 'ipAddress' => $ipAddress])->throw();
                \Log::info($_SERVER['HTTP_HOST'].': billing-history request generated => '.$entity);
            } else {
                \Log::info($_SERVER['HTTP_HOST'].': Site plan id is not found for billing-history.');
            }
        } catch (RequestException $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Error in billing-history " . $payloadUrl . ' >> ' . $e->response->json()['message']);
        }
    }

        public static function get_col_enum_arr($table, $column)
    {
        if(isset($table) || isset($column)){
        $type = DB::select(DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$column.'"'))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = array();
        foreach(explode(',', $matches[1]) as $value){
            $values[] = trim($value, "'");
        }
        sort($values);
        return $values;
      } else {
        return null;
      }
    }

    public static function check_investment_users($investment_id,$user_id)
    {
        $result = DB::table('deal_user')->where('deal_id',$investment_id)->where('user_id',$user_id)->exists();
        if($result){
            return 1;
        } else{
            return 0;
        }
    }

     public static function ep_fee_transaction($site_id, $txnType, $particular, $amount, $type=null, $ptxn_id=null)
    {
        $siteDetail  = Sites::select('payin','payout','payin_fixed','payout_fixed')->where('id', $site_id)->first();

        if($siteDetail->payin=='' && $siteDetail->payout==''){
            if($siteDetail->payin_fixed=='' && $siteDetail->payout_fixed==''){
                return false;
            }
        }

        if($txnType=='payin' && $siteDetail->payin!=''){
            $fee = $amount*($siteDetail->payin/100);
        } elseif($txnType=='payout' && $siteDetail->payout!=''){
            $fee = $amount*($siteDetail->payout/100);
        } elseif($txnType=='payout' && $siteDetail->payout_fixed!='') {
            $fee = $siteDetail->payout_fixed;
        } elseif($txnType=='payin' && $siteDetail->payin_fixed!='') {
            $fee = $siteDetail->payin_fixed;
        } else {
            return false;
        }
        $exclusive_gst_ep_fee = round($fee, 2);
        // calculate gst
        $gstfee = $exclusive_gst_ep_fee*(18/100);
        $ep_fee = round($exclusive_gst_ep_fee+$gstfee, 2);
        $trustmore_id  = Sites::select('id')->where('site_code', 'TRUST')->first()->id;
        $accountinfo = Escrow::where('user_id', $site_id)->where('ac_type', -2)->first();
        $partyinfoinfo = Escrow::where('user_id', $trustmore_id)->where('ac_type', -1)->first();

        $account_from_ac = $accountinfo->id;
        $account_site_id = $accountinfo->site_id;

        $party_to_ac = $partyinfoinfo->id;
        $party_site_id = $partyinfoinfo->site_id;

        if ($ptxn_id != null) {
            $escroTxn = DB::table('virtual_escrow_txns')->where('id',$ptxn_id)->first();
            if($escroTxn != null && $escroTxn->account_id == $account_from_ac) {
                \Log::info($_SERVER['HTTP_HOST'].": EP Fee cannot be debited from Fee Account");
                return false;
            }
        }


       try {
        if($type==null){

        \Log::info($_SERVER['HTTP_HOST'].": Pre - EP Fee Transactions amount ".$ep_fee." from ".$account_from_ac." to ".$party_to_ac);
            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $ep_fee, $account_from_ac, $party_to_ac, null, 1, null, null, null, $ptxn_id);
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $ep_fee, $party_to_ac, $account_from_ac, null, 1, null, null, null, $ptxn_id);
            \Log::info($_SERVER['HTTP_HOST'].": Post - EP Fee Transactions amount ".$ep_fee." from ".$account_from_ac." to ".$party_to_ac);

        } else {
            \Log::info($_SERVER['HTTP_HOST'].": Pre - EP Fee Transactions amount ".$ep_fee." from ".$party_to_ac." to ".$party_to_ac);
            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $ep_fee, $party_to_ac, $account_from_ac, null, 1, null, null, null, $ptxn_id);
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $ep_fee, $account_from_ac, $party_to_ac, null, 1, null, null, null, $ptxn_id);

            \Log::info($_SERVER['HTTP_HOST'].": Post - EP Fee Transactions amount ".$ep_fee." from ".$party_to_ac." to ".$party_to_ac);

        }
            return true;

        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": EP Fee Transactions Failed Error=".$ex->getMessage());
            return false;
        }
    }

    public function esign_fee($site_id,$particular,$amount)
    {
        try {
            $fee_acc = Escrow::where('user_id', $site_id)->where('ac_type', -2)->first(); //fee account
            $party_ac = Escrow::where('user_id', 1)->where('ac_type', -1)->first();
            $amountIncGST = $amount + round($amount *0.18,2);

            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $amountIncGST, $fee_acc->id, $party_ac->id, null, 1, null, null, null);
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $amountIncGST, $party_ac->id, $fee_acc->id, null, 1, null, null, null);
            return true;
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": eSign Fee Transaction Failed with Error=".$ex->getMessage());
            return false;
        }
    }

    public function pg_charges_transaction($site_id,$particular,$amount)
    {
        try{
            if($amount > 0){
                $accountFrom = Escrow::where('user_id', $site_id)->where('ac_type', -1)->first();
                $trustmore_id  = Sites::select('id')->where('site_code', 'TRUST')->first()->id;
                $partyTo = Escrow::where('user_id', $trustmore_id)->where('ac_type', -1)->first();

                BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $amount, $accountFrom->id, $partyTo->id, null, 1, null, null, null);
                BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $amount, $partyTo->id, $accountFrom->id, null, 1, null, null, null);
            }
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": PG Charges Transaction Failed with Error=".$ex->getMessage());
            return false;
        }
    }

    public static function generateEscrowFeeAcNo($siteid)
    {
        $getBankCode = get_bank_code($siteid);
        if($getBankCode == 'axis-bank'){
            $corpCode = env('AXIS_AC_CORP_CODE','ABCD');
            $bank_name = 'AXIS';
            $ifsc_code = 'UTIB0CCH274';
        } elseif($getBankCode == 'indusind-bank'){
            $corpCode = env('IBL_AC_CORP_CODE','ZDREAM');
            $bank_name = 'Indusind';
            $ifsc_code = 'INDB0000001'; //@TODO
        } else {
            $corpCode = 'EPUG';
            $bank_name = 'ICICI';
            $ifsc_code = 'ICIC0000104';
        }

        $sitecode  = Sites::where('id', $siteid)->first()->site_code;
        $accountno = strtoupper($corpCode . $sitecode . str_pad($siteid, 3, '0', STR_PAD_LEFT));

        DB::table('virtual_escrow_accounts')->insert([
            'site_id'      => $siteid,
            'user_id'      => $siteid,
            'escrow_ac_no' => $accountno,
            'ac_type'      => -2,
            'payin_src'    => null,
            'balance'      => 0,
            'bank_name'    => $bank_name,
            'ifsc_code'    => $ifsc_code,
            'created_at'   => date('Y-m-d H:i:s'),
            'status'       => 1,
        ]);
    }

    public static function getStringFirstTwoChar($string)
    {
        $string = $string != '' ? $string : '';
        return strtoupper(substr($string, 0, 2));
    }

    public function ugroCollectionRelease($receiver_id,$sender_id , $dealId,$amount, $site_id=null)
    {
        //DB Transaction - BEGINS
        \DB::beginTransaction();
        try {
            $deal = Deal::with('dealusers')->with('associatedoc')->where('id', $dealId)->first();
            // Check if this user is part of this Deal
            $chkUser = false; $userType;
            foreach ($deal->dealusers as $user) {
                if ($user->pivot->user_id == $receiver_id) {
                    $userType = $user->user_type;
                    $chkUser = true;
                    break;
                }
            }

            if (!$chkUser) {
                return array('success' => false, 'message' => 'This user does not belong to this collection');
            }
            if(null==$site_id){
                $site_id = epcache('site_id');
            }
            $site         = Sites::where('id', $site_id)->first();
            $dealEscrowAc = Escrow::where('user_id', $sender_id)->where('ac_type', 4)->where('site_id', $site->id)->first();
            if ($amount > $dealEscrowAc->balance) {
                return array('success' => false, 'message' => 'Escrow account balance is not sufficient: ' . $dealEscrowAc->balance);
            }
            $user = User::where('id',$receiver_id)->first();
            $userEscrowAc = Escrow::where('user_id', $receiver_id)->where('ac_type', 0)->where('site_id', $site->id)->first();
            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, "Fund Released to ".$user->name, $amount,  $dealEscrowAc->id,$userEscrowAc->id,null, 1, null);
            //$site_id, $type, $txn_type, $particular, $amount, $receiver_id, $sender_id, $bankid, $status, $utr_no, $payout_id = null, $party_name = null, $ptxn_id = null, $no_of_days = null
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, "Fund Received from Deal #".$deal->id, $amount, $userEscrowAc->id, $dealEscrowAc->id, null, 1, null);

            // Withdraw to Bank API needs to be called explicitly
            //if($userType == 1){
                BasicHelper::withdrawal_request($site_id, $amount, $userEscrowAc->id);
            //}

        } catch (\Exception $ex) {
            Logger::error("Exception Occurred while Releasing Collection Fund");
            Logger::error($ex);
            \DB::rollBack();
            return array('success' => false, 'message' => 'Collection Funds Release Failed - ' . $ex->getMessage());
        }
        \DB::commit();
        // DB Transaction - ENDS
        \Log::info(": Collection ".$dealId." Funds Released Successfully.");
        return array('success' => true, 'message' => 'Collection Funds Released Successfully.');
    }

    public function sendConsent($request, $user_id, $sitedata)
    {
        try {
            $site_id = $sitedata->id;
            $user_ip = getUserIpAddr();
            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
            $lat = $geo["geoplugin_latitude"];
            $long = $geo["geoplugin_longitude"];
            $location = array($lat, $long);

            Consent::updateOrCreate([
                'user_id' => $user_id,
                'site_id' => $site_id,
                'file_path' => $sitedata->terms,
                'ip' => $user_ip,
                'location' => json_encode($location),
                'consent_status' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $agreement = Agreement::updateOrCreate([
                    'site_id' => $site_id,
                    'user_id' => $user_id,
                    'url' => $sitedata->terms
                ],
                [
                    'doc_type' => 3,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            $associated_deals = Self::getDeals($user_id);
            foreach($associated_deals as $deal) {
                Dealdoc::updateOrCreate([
                    'deal_id' => $deal->deal_id,
                    'doc_type' => 1,
                    'doc_name' => 'EAAA',
                    'doc_path' => $sitedata->terms,
                    'status' => 1,
                    'user_id' => $user_id
                ],
                [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            AgreementSignings::updateOrCreate([
                'agreement_id' => $agreement->id,
                'signer_id' => $user_id
            ],
            [
                'status' => 1,
                'etype' => 'agreement',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            Logger::info('Consent request received for user: '.$user_id);

        }
        catch (\Exception $ex) {
            Logger::error("Exception Occurred while Sending consent from UI");
            Logger::error($ex);
            return response()->json(['success' => false, 'message' => 'Consent could not be sent from UI- ' . $ex->getMessage()], 400);
        }
    }

    public function getDeals($user_id) {
        try {
            $deals = DB::table('deal_user')->select('deal_id')->where('user_id', $user_id)->get();
            return $deals;
        } catch (\Exception $ex) {
            Logger::error("Exception Occurred while fetching associated deals: ");
            Logger::error($ex);
        }
    }

    /******* AXIS BANK CODE STARTS  *********/

    public function generateCheckSum($requestMap)
    {
        $finalChkSum = '';
        $keys = '';
        try {
            if ($requestMap === null) {
                return null;
            }

            foreach ($requestMap as $key => $value) {
                $val_check = is_array($value);
                if ($key !== 'checksum') {
                    if ($val_check) {
                        $innerObjectMap2 = $value;
                        foreach ($innerObjectMap2 as $entryInnKey => $entryInnValue) {
                            $keys .= $entryInnKey;
                            $finalChkSum .= self::validateInfo(strval($entryInnValue));
                        }
                    } else {
                        $finalChkSum .= self::validateInfo(strval($value));
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e);
            Logger::error($e);
        }
        \Log::info("Generated Checksum [RAW]: ".$finalChkSum);
        return self::encodeCheckSumWithSHA256($finalChkSum);
    }

    public function getInnerLevel2Map($entryInnLvl2, $finalChkSum123)
    {
        $finalChkSum = '';
        $keys = '';

        if (is_array($entryInnLvl2)) {
            $tempLst = $entryInnLvl2;
            if (!empty($tempLst) && is_array($tempLst[0]) && is_array($tempLst[0])) {
                $innerObjectMap = $entryInnLvl2;

                foreach ($innerObjectMap as $innerMap) {
                    foreach ($innerMap as $entryInnKey => $entryInnValue) {
                        $keys .= $entryInnKey;
                        $finalChkSum .= self::validateInfo(strval($entryInnValue));
                    }
                }
            } elseif (!empty($tempLst)) {
                foreach ($tempLst as $strValues) {
                    $finalChkSum .= self::validateInfo(strval($strValues));
                }
            }
        } elseif (is_array($entryInnLvl2)) {
            $innerObjectMap2 = $entryInnLvl2;

            foreach ($innerObjectMap2 as $entryInnKey => $entryInnValue) {
                $keys .= $entryInnKey;
                $finalChkSum .= self::validateInfo(strval($entryInnValue));
            }
        } else {
            $finalChkSum .= self::validateInfo(strval($entryInnLvl2));
        }

        return $finalChkSum;
    }

    public function encodeCheckSumWithSHA256($data)
    {
        $response = null;
        try {
        $hashBytes = hash('md5', $data, true);
        $response = bin2hex($hashBytes);
        } catch (Exception $e) {
        throw new RuntimeException("Internal server error");
        }
        return $response;
    }

    public function validateInfo($value)
    {
        if (!empty($value) && $value !== "null") {
            return $value;
        } else {
            return "";
        }
    }

    public static function iblPaymentApi($apiType, $data)
    {
        try {
            \Log::info("REQUEST [PLAIN] : ".json_encode($data));
            $data = json_encode($data);
            $output = shell_exec('java -cp '.env('IBL_API_JAR').' com.indusind.api.client.IBLEncrypt '.$data);
            $encReqFull = json_decode($output);
            $encodedKey = $encReqFull->encodedKey;
            \Log::info("ENCODED KEY : ".$encodedKey);
            unset($encReqFull->encodedKey);
            $encRequest = json_encode($encReqFull);
            \Log::info("REQUEST [ENCRYPTED] : ".$encRequest);

            //$uri = $apiType == 'P' ? '/sync-apis/ISync/ProcessTxn' : '/sync-apis/ISync/StatusEnq';
            switch ($apiType) {
                case "P": // SYNC Process Payment
                    $uri = '/sync-apis/ISync/ProcessTxn';
                    $mesg = "PROCESS PAYMENT";
                    break;
                case "S": // SYNC Payment Status
                    $uri = '/sync-apis/ISync/StatusEnq';
                    $mesg = "PAYMENT STATUS ENQUIRY";
                    break;
                case "G": // IEC Get Tender
                    $uri = '/iec/etender/getTenderId/v1';
                    $mesg = "GET TENDER ID";
                    break;
                case "U": // IEC Update Tender
                    $uri = '/iec/etender/updateTenderId/v1';
                    $mesg = "UPDATE TENDER ID";
                    break;
            }

            $url = env('IBL_API_URL','https://indusapiuat.indusind.com/indusapi-np/uat').$uri;
            $encResFull = BasicHelper::iblApiCall($encRequest, $url);
            $encResponse = json_decode($encResFull)->data;
            \Log::info("RESPONSE [ENCRYPTED] : ". $encResponse);

            $output = shell_exec('java -cp '.env('IBL_API_JAR').' com.indusind.api.client.IBLDecrypt '.$encodedKey.' '.$encResponse);
            \Log::info("RESPONSE [PLAIN] : ". $output);

            return json_decode($output);
        } catch (Exception $ex) {
            \Log::error("Exception occured in Encryption / Decryption for IBL ".$mesg. " API : " . $ex->getMessage());
            return null;
        }
    }

    public static function iblApiCall($data, $url)
    {
        try {
            $clientId = env('IBL_CLIENT_ID','1f3665d270d1cc5a5760bbf8dc1e1de1');
            $clientSecret = env('IBL_CLIENT_SECRET','8c8e6ef81377605bb798a53f8abd4c57');

            $headers = [
                'Content-Type' => 'application/json',
                'IBL-Client-Id' => $clientId,
                'IBL-Client-Secret' => $clientSecret,
            ];

            $response = Http::withOptions([])
            ->withHeaders($headers)
            ->post($url, json_decode($data,true));

            return $response->body();
        } catch (Exception $ex) {
            \Log::error('Exception occured while calling IBL API '. $url . " : " . $ex->getMessage());
            return false;
        }
    }

    public function axisApiCall($data, $url)
    {
        try {
            \Log::info("Inside axisApiCall : REQUEST BODY : ".$data);
            $keyPath = env('AXIS_CSR_KEY','/etc/ssl/escrowpayindia.key');
            $passphrase = env('AXIS_CSR_PASS','trustmore##');
            $clientId = env('AXIS_CLIENT_ID','290d204e18f0ee3b3eaecdcbc16ab9d3');
            $clientSecret = env('AXIS_CLIENT_SECRET','c00a4e021e8ab73929b19b736b10ad3b');

            $headers = [
                'Content-Type' => 'application/json',
                'X-IBM-Client-Id' => $clientId,
                'X-IBM-Client-Secret' => $clientSecret,
            ];

            $response = Http::withOptions([
                'cert' => env('AXIS_CSR_CERT','/etc/ssl/signed.crt'),
                'ssl_key' => [$keyPath, $passphrase],
            ])
            ->withHeaders($headers)
            ->post($url, json_decode($data,true));
            \Log::info("AXIS RESPONSE : ".$response);
            return $response->body();
        } catch (Exception $ex) {
            \Log::error('Exception occured while axis bank api call: '. $ex->getMessage());
            return false;
        }
    }

    public function aes128Encrypt($plainText)
    {
        $cipher = "aes-128-cbc";
        $key = env('AXIS_ENCRYPT_KEY','7DE7591C370B2AC68B9331FBE182F1B7');
        $iv = hex2bin("8E12399C07726F5A8E12399C07726F5A");
        // Create a cipher instance with the algorithm and padding
        $skeySpec = hex2bin($key); // IV bytes

        // Generating encrypted result
        $encrypted = openssl_encrypt($plainText, $cipher, $skeySpec, OPENSSL_RAW_DATA, $iv);

        // To add IV in encrypted string
        $encryptedWithIV = $iv . $encrypted;

        $encryptedResult = base64_encode($encryptedWithIV);
        return $encryptedResult;
    }

    public function aes128Decrypt($encryptedText)
    {
        $cipher = "aes-128-cbc";
        $key = env('AXIS_ENCRYPT_KEY','7DE7591C370B2AC68B9331FBE182F1B7');
         // Create a cipher instance with the algorithm and padding
        $skeySpec = hex2bin($key); // IV bytes
        $encryptedIVandTextAsBytes = base64_decode($encryptedText);
        $iv = substr($encryptedIVandTextAsBytes, 0, 16);
        $ciphertextByte = substr($encryptedIVandTextAsBytes, 16);
         // Generating encrypted result
        $decryptedTextBytes = openssl_decrypt($ciphertextByte,$cipher, $skeySpec, OPENSSL_RAW_DATA, $iv);
        if ($decryptedTextBytes === false) {
            // Check for errors in decryption
            $error = openssl_error_string();
            \Log::error("OpenSSL Error: " . $error);
            return $error;
        } else {
            $decryptedResult = mb_convert_encoding($decryptedTextBytes, "UTF-8");
            return $decryptedResult;
        }
    }

    public static function generateAxisEscrowAcNo($siteid, $user, $userpan, $actype, $payinsrc, $created_at)
    {
        $axisCorpCode = env('AXIS_AC_CORP_CODE','ABCD');
        $sitecode  = Sites::where('id', $siteid)->first()->site_code;
        $accountno = strtoupper($axisCorpCode . $sitecode . $userpan . (($payinsrc == '') ? '' : $payinsrc));

        DB::table('virtual_escrow_accounts')->insert([
            'site_id'      => $siteid,
            'user_id'      => $user,
            'escrow_ac_no' => $accountno,
            'ac_type'      => $actype,
            'payin_src'    => ($payinsrc == '') ? null : $payinsrc,
            'balance'      => 0,
            'bank_name'    => 'AXIS',
            'ifsc_code'    => 'UTIB0CCH274',
            'created_at'   => $created_at == 0 ? date('Y-m-d H:i:s') : $created_at,
            'status'       => 0,
        ]);
    }
    /******* AXIS BANK CODE ENDS  ******/
    public function axisEnachEncryptDecrypt($mode, $inputstring)
    {
        $key = env('AXIS_ENACH_ENCRYPT_DECRYPT_KEY','Ua2P+vSssa0pkFMm');
        $id = env('AXIS_ENACH_ENCRYPTION_ID','c7a0fb9359c87d14b6d0445e0417b0384e4c18374e14946cc3dbca549d77f4ef');

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $key;
        $IV = random_bytes(16);
        $hasing_iv = hash('sha256', $secret_key,true);
        $hasing_iv = bin2hex($hasing_iv);
        $substring_iv = substr($hasing_iv, 0, 32);

        if ( $mode == 'encrypt') {
            $output = openssl_encrypt($inputstring, $encrypt_method, $substring_iv, 0, $IV);
            $output = str_replace(['+', '/'], ['-', '_'], $output);
            $output = $id .'.'.bin2hex($IV).'.'. $output;
        }
        else if( $mode == 'decrypt') {
            $encdata = explode('.', $inputstring);

            if(isset($encdata[0])){
                $IV = hex2bin($encdata[0]);
            }
            if(isset($encdata[2])){
                $encryptdata = str_replace(['-', '_'], ['+', '/'], $encdata[2]);
            }else {
                $encryptdata = str_replace(['-', '_'], ['+', '/'], $encdata[1]);
            }

            $output = openssl_decrypt($encryptdata, $encrypt_method, $substring_iv, 0, $IV);
        }
        return $output;
    }

    public function pineHashGeneration($hex,$prerequest)
    {
        $secret_key = "";
        for ($i=0; $i < strlen($hex)-1; $i+=2) {
            $secret_key .= chr(hexdec($hex[$i].$hex[$i+1]));
        }

        return strtoupper(hash_hmac('SHA256', $prerequest, $secret_key));
    }

    public function generatePaymentGatewayURL($payment_id)
    {
        $pid = epcache('site_id')."|".$payment_id;
        return url('payment/initiate/'.base64_encode(aes128EncryptGlobal($pid)));
    }

    public function generatePayloadAuth($checksum,$secret_key)
    {
        return strtoupper(hash_hmac('sha256', $checksum, $secret_key));
    }

    public function axisEnachTxnEncryptDecrypt($mode, $inputstring)
    {
        \Log::info("AXIS ENACH TXN EN-DE with mode =".$mode.", inputstring =".$inputstring);
        $key = env('AXIS_ENACH_TOKEN_ID','U555822DCA7C2950990F6A9258583E1D3A013339E2B19AC390D5D0FF647AUAT5');
        $encrypt_method = "AES-256-CBC";
        $output = "";

        $byteKeytemp = mb_convert_encoding($key, 'UTF-8');
        $hashbyte = hash('sha256', $byteKeytemp, true);
        $hashstring = bin2hex($hashbyte);

        if (strlen($hashstring) > 32) {
            $hashstring = substr($hashstring, 0, 32);
        }

        $byteKeytemp = mb_convert_encoding($hashstring, 'UTF-8');
        $byteKey = str_pad($byteKeytemp, 32, "\0");
        $byteIvtemp = mb_convert_encoding($key, 'UTF-8');
        $hashbyte = hash('sha256', $byteIvtemp, true);
        $hashstring = bin2hex($hashbyte);
        $byteIvtemp = mb_convert_encoding($hashstring, 'UTF-8');

        if ( $mode == 'encrypt') {
            $iv = random_bytes(16);
            $byteEncString = mb_convert_encoding($inputstring, 'UTF-8');
            $byteoutput = openssl_encrypt($byteEncString, $encrypt_method, $byteKey, OPENSSL_RAW_DATA, $iv);
            $output = bin2hex($iv) . '.' . base64_encode($byteoutput);
            $output = str_replace(['+', '/'], ['-', '_'], $output);
        } else if( $mode == 'decrypt') {
            $parts = explode('.', $inputstring);
			$ivString = $parts[0];
			$data = $parts[1];
			$iv =  hex2bin($ivString);
			$data = str_replace(['-', '_'], ['+', '/'],  $data);
			$data = base64_decode($data);
			$outstring = openssl_decrypt($data, $encrypt_method, $byteKey, OPENSSL_RAW_DATA, $iv);
        }
        return $output;
    }

    public function getRecurPayloadURL($siteCode)
    {
        $recur_site = DB::table('sites')->select('id','site_name','site_code','site_domain')->where('site_code',$siteCode)->first();
        $protocol = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")?'http://':'https://';
        return $protocol.$recur_site->site_domain;
    }

    public function mandateStatusAxisApiCall($data)
    {
        $request = '{
            "created_date" : "'.date("d-M-Y", strtotime($data['created_at'])).'",
            "trxn_no" : "'.$data['txn_id'].'",
            "merchantid" : "'.env('AXIS_ENACH_MERCHANT_ID','884577').'",
            "subbillerid" : "'.env('AXIS_ENACH_SUBBILLER_ID','661507').'",
            "status" : "'.$data['reqStatus'].'"
        }';

        $encrypt = BasicHelper::axisEnachEncryptDecrypt('encrypt',$request);
        $data = '{"param":"'.$encrypt.'"}';
        \Log::info("Axis Enach Status Request [ ENCRYPT ]:".$data);

        $keyPath = env('AXIS_CSR_KEY','/etc/ssl/escrowpayindia.key');
        $passphrase = env('AXIS_CSR_PASS','trustmore##');
        $url = env('AXIS_ENACH_URL','https://uat.camspay.com').'/api/v1/enachStatusCheck';

        $headers = [
            'Content-Type' => 'application/json',
        ];
        $response = Http::withOptions([
            'cert' => env('AXIS_CSR_CERT','/etc/ssl/signed.crt'),
            'ssl_key' => [$keyPath, $passphrase],
        ])
        ->withHeaders($headers)
        ->post($url, json_decode($data,true));

        \Log::info("Axis Enach Status Response [ ENCRYPT ] :".$response);
        $response = json_decode($response,true);

        $decryptedData = BasicHelper::axisEnachEncryptDecrypt('decrypt',$response['res']);
        \Log::info("Axis Enach Manddate Status Response [RAW] : ".$decryptedData);
        return json_decode($decryptedData,true);
    }

    public function txnStatusAxisApiCall($txn_id)
    {
        $tokenId = env('AXIS_ENACH_TOKEN_ID','U555822DCA7C2950990F6A9258583E1D3A013339E2B19AC390D5D0FF647AUAT5');
        $tokenKey = env('AXIS_ENACH_TOKEN_KEY','9GHJ307EC0E7A4A885DA219AF2E48397F9217FC065FCD4616F05B219409A5555');

        $correlation = Str::upper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 36));
        $timestamp =  date('dmYHis', time()); //Get current date and time in ddMMyyyyHHmmss format
        $headers = [
            'Authorization' => $tokenId.":".$tokenKey,
            'TOKEN_ID' => $tokenId,
            'TOKEN_KEY' => $tokenKey,
            'Correlation' => $correlation,
            'Timestamp' => $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        \Log::info("HEADERS == ".json_encode($headers));
        $request = '{ "TRANSACTION_ID" : "'.$txn_id.'" }';
        $url = env('AXIS_ENACH_TXN_URL','https://nachsuite.camspay.com').'/EPS_TRANSACT_API/TransactInquiry';

        $response = Http::withHeaders($headers)
        ->post($url, json_decode($request,true));
        \Log::info("Axis Enach Txn Status Response [RAW] : ".$response);
        return json_decode($response,true);
    }

    public function payinProcess($data)
    {
        $paymentdata = array('reference_no' => $data['txn_id'], 'site_id' => $data['site_id'], 'response_status' => $data['response_desc'], 'gateway_id' => $data['gateway_id'], 'user_id' => $data['user_id'], 'account1_id' => $data['account1_id'],'deal_id' => $data['deal_id'], 'utr_no' => $data['utr_no'], 'payment_mode' => $data['payt_mode'], 'total_amount' => $data['amount'], 'response_message' => json_encode($data), 'created_at' => date("Y-m-d H:i:s") );
        $payment_id = DB::table('payments')->insertGetId($paymentdata);

        /** Webhook Call code starts */
        $webhookData = array('site_id'=>$data['site_id'],'data_type'=>"payin","txn_id"=>$data['txn_id'],"utr_no"=>$data['utr_no'],"user_id"=>$data['user_id'],"amount"=>$data['amount'],"balance"=>$data['balance'],"payment_id"=>$payment_id,"payment_status"=>1,"payment_mode"=>$data['payt_mode'],"txn_date"=>$data['txn_time'],"cust_ref_no"=>null,"pg_ref_no"=>$data['txn_id'],"sender_ac_no"=>$data['sender_ac_no'],"sender_ifsc"=>$data['sender_ifsc'],"sender_name"=>$data['sender_name']);
        webhookCallNew($webhookData);
        /** Webhook call code ends */

        return $payment_id;
    }

    public function getBankCorpCode($bank)
    {
        if((isset($bank) && $bank == 'axis-bank')){
            $corpCode = env('AXIS_AC_CORP_CODE','ABCD');
            $bank_name = 'AXIS';
            $ifsc_code = 'UTIB0CCH274';
        } elseif($bank == 'indusind-bank'){
            $corpCode = env('IBL_AC_CORP_CODE','ZDREAM');
            $bank_name = 'Indusind';
            $ifsc_code = 'INDB0000001'; //@TODO
        } else {
            $corpCode = 'EPUG';
            $bank_name = 'ICICI';
            $ifsc_code = 'ICIC0000104';
        }
        return [
            'corpCode' => $corpCode,
            'bank_name' => $bank_name,
            'ifsc_code' => $ifsc_code
        ];
    }

    // Axis Bank UPI AutoPay Encryption Logic
    function encryptAxisUpiAutopay($message) {
        // Load the public key
        $publicKey = file_get_contents(env('AXIS_UPI_PUB_KEY'));

        // Encrypt the message
        $encrypted = '';
        openssl_public_encrypt($message, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);

        // Convert the encrypted bytes to hexadecimal representation
        $hexEncrypted = bin2hex($encrypted);
        return $hexEncrypted;
    }

    // Axis Bank UPI AutoPay Decryption Logic
    function decryptAxisUpiAutopay($encryptedText) {
        // Get the first 16 characters of the keyString
        $key = substr(env('AXIS_UPI_DEC_KEY'), 0, 16);

        // Decode the base64 encoded encrypted text
        $decodedValue = base64_decode($encryptedText);

        // Perform the decryption
        $response = openssl_decrypt($decodedValue, 'aes-128-ecb', $key, OPENSSL_RAW_DATA);
        return $response;
    }

    public static function axisUPIApiCall($apiType,$request)
    {
        $uri = '';
        try{
            $apiUrl = env('AXIS_AUTOPAY_URL','https://upiuat.axisbank.co.in/m2');
            $data = json_decode($request,true);
            $merchantId = $data['merchId'];
            $merchantChannelId = $data['merchChanId'];
            $txnId = $data['txnId'];

            switch ($apiType) {
                case "V": //validate Vpa
                    $uri = $apiUrl.'/mandate/verifyvpa';
                    $msg = 'Validate Vpa';
                    break;
                case "C": // Mandate Create
                    $uri = $apiUrl.'/mandateCreate';
                    $msg = 'Mandate Create';
                    break;
                case "E": // Mandate Exceute/Txn Initiate
                    $uri = $apiUrl.'/executeMandate';
                    $msg = 'Mandate Exceute';
                    break;
                case "M": // Mandate Modify/Update
                    $uri = $apiUrl.'/modifyMandate';
                    $msg = 'Mandate Update';
                    break;
                case "R": // Mandate Revoke/Cancel
                    $uri = $apiUrl.'/revokeMandate';
                    $msg = 'Mandate Revoke';
                    break;
                case "S": // Mandate Status
                    $uri = $apiUrl.'/bank/chkTxnMandate';
                    $msg = 'Mandate Status';
                    break;
                case "N": // Mandate Status
                    $uri = $apiUrl.'/bank/transactions/mandatenotification';
                    $msg = 'Mandate Notify';
                    break;
            }
            $url = $uri. "?txnId={$txnId}&merchId={$merchantId}";
            \Log::info("AXIS UPI AUTOPAY - URL : ".$url);
            $response = Http::withHeaders([ 'Content-Type' => 'application/json' ])->post($url,$data);
            if ($response->getStatusCode() != 200) {
                throw new \Exception('HTTP Response Code is NOT 200 OK, its '.$response->getStatusCode());
            }
            return $response->body();
        } catch (Exception $ex) {
            Logger::error('Exception occured while calling UPI Autopay API '. $uri);
            Logger::error($ex);
            return false;
        }
    }

    public static function upiAutopayEncryptDecrypt($data, $initVector = null, $mode = 'encrypt')
    {
        // AES Decryption logic for callback
        $keyStr = 'SiZ0ir43GcHvolsL';
        // If initVector is not provided, use the first 16 characters of the keyStr as initVector
        if ($initVector === null) {
            $initVector = substr($keyStr, 0, 16);
        }

        // Extract the key from keyStr
        $key = substr($keyStr, 0, 16);

        // Determine the OpenSSL encryption/decryption mode based on the $mode variable
        $opensslMode = $mode === 'encrypt' ? 'AES-128-CBC' : 'AES-128-CBC';

        if ($mode === 'encrypt') {
            // Encrypt the data
            $encrypted = openssl_encrypt($data, $opensslMode, $key, OPENSSL_RAW_DATA, $initVector);

            // Return base64 encoded encrypted data
            return base64_encode($encrypted);
        } elseif ($mode === 'decrypt') {
            // Decrypt the base64 encoded encrypted data
            $decrypted = openssl_decrypt(base64_decode($data), $opensslMode, $key, OPENSSL_RAW_DATA, $initVector);

            // Return the decrypted data
            return $decrypted;
        } else {
            // Invalid mode
            return null;
        }
    }


    public static function onboarding_fee($user_id, $site_id, $particular, $amount, $type=null, $ptxn_id=null)
    {
        try {
            $creditedFrom = Escrow::where('user_id',$user_id)->where('site_id',$site_id)->where('ac_type',0)->first();
            $siteAcc = Escrow::where('user_id', $site_id)->where('ac_type', -1)->first();
            \Log::info("account Form :: ".json_encode($siteAcc));
            $trustmore_id  = Sites::select('id')->where('site_code', 'TRUST')->first()->id;
            \Log::info("trustmore_id :: ".$trustmore_id);
            $partyTo = Escrow::where('user_id', $trustmore_id)->where('ac_type', -1)->first();
            \Log::info("partyTo :: ".json_encode($partyTo));

            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $amount, $creditedFrom->id, $siteAcc->id, null, 1, null, null, null);
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $amount, $siteAcc->id, $creditedFrom->id, null, 1, null, null, null);

            BasicHelper::escrow_account_transaction($site_id, 'debit', 0, $particular, $amount, $siteAcc->id, $partyTo->id, null, 1, null, null, null);
            BasicHelper::escrow_account_transaction($site_id, 'credit', 0, $particular, $amount, $partyTo->id, $siteAcc->id, null, 1, null, null, null);
            return true;
        } catch (\Exception $ex) {
            \Log::error($_SERVER['HTTP_HOST'].": Onboarding Fee Transactions Failed Error=".$ex->getMessage());
            return false;
        }
    }

} //end
