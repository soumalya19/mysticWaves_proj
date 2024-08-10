<?php 

  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');


  require('inc/Razorpay/razorpay.php');
  require('inc/Razorpay/payment-success.php');
  require('inc/Razorpay/payment-failed.php');

  date_default_timezone_set("Asia/Kolkata");

  session_start();

  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('inc/Razorpay/razorpay.php');
  }

  if(isset($_POST['action']) && $_POST['action']='payOrder'){

    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods:POST,GET,PUT,PATCH,DELETE');
    header("Content-Type: application/json");
    header("Accept: application/json");
    header('Access-Control-Allow-Headers:Access-Control-Allow-Origin,Access-Control-Allow-Methods,Content-Type');

    $razorpay_mode='test';
    
    $razorpay_test_key='rzp_test_l9HqHJugsHFXws'; //Your Test Key
    $razorpay_test_secret_key='ULwNHZ0wyl2PSAA8UXHIQp1P'; //Your Test Secret Key
    
    $razorpay_live_key= 'Your_Live_Key';
    $razorpay_live_secret_key='Your_Live_Secret_Key';
    
    if($razorpay_mode=='test'){
        
        $razorpay_key=$razorpay_test_key;
        
    $authAPIkey="Basic ".base64_encode($razorpay_test_key.":".$razorpay_test_secret_key);
    
    }else{
        
      $authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
      $razorpay_key=$razorpay_live_key;
    
    }
    
    // Set transaction details
    $order_id = uniqid(); 
    
    $billing_name=$_POST['billing_name'];
    $billing_mobile=$_POST['billing_mobile'];
    $billing_email=$_POST['billing_email'];
    $shipping_name=$_POST['shipping_name'];
    $shipping_mobile=$_POST['shipping_mobile'];
    $shipping_email=$_POST['shipping_email'];
    $paymentOption=$_POST['paymentOption'];
    $payAmount=$_POST['payAmount'];
    
    $note="Payment of amount Rs. ".$payAmount;
    
    $postdata=array(
    "amount"=>$payAmount*100,
    "currency"=> "INR",
    "receipt"=> $note,
    "notes" =>array(
                "notes_key_1"=> $note,
                "notes_key_2"=> ""
                  )
    );
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>json_encode($postdata),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: '.$authAPIkey
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    $orderRes= json_decode($response);
     
    if(isset($orderRes->id)){
     
    $rpay_order_id=$orderRes->id;
     
    $dataArr=array(
      'amount'=>$payAmount,
      'description'=>"Pay bill of Rs. ".$payAmount,
      'rpay_order_id'=>$rpay_order_id,
      'name'=>$billing_name,
      'email'=>$billing_email,
      'mobile'=>$billing_mobile
    );
    echo json_encode(['res'=>'success','order_number'=>$order_id,'userData'=>$dataArr,'razorpay_key'=>$razorpay_key]); exit;
    }else{
      echo json_encode(['res'=>'error','order_id'=>$order_id,'info'=>'Error with payment']); exit;
    }
    }else{
        echo json_encode(['res'=>'error']); exit;
    }


    // Insert payment data into database

    $frm_data = filteration($_POST);

    $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,`order_id`) VALUES (?,?,?,?,?)";

    insert($query1,[$CUST_ID,$_SESSION['room']['id'],$frm_data['checkin'],
      $frm_data['checkout'],$ORDER_ID],'issss');
    
    $booking_id = mysqli_insert_id($con);

    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,
      `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";

    insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],
      $TXN_AMOUNT,$frm_data['name'],$frm_data['phonenum'],$frm_data['address']],'issssss');


?>

<html>
<head>
<title>How to Integrate Razorpay payment gateway in PHP | tutorialswebsite.com</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="screen">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<link href="D:\XAMPP\htdocs\MysticWaves\pay_now">

</head>
<body style="background-repeat: no-repeat; margin-top:30px;">
<div class="container">
<div class="row">
<div class="col-xs-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Charge Rs.100 INR  </h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="billing_name" id="billing_name" placeholder="Enter name" required="" autofocus="">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="billing_email" id="billing_email" placeholder="Enter email" required="">
                        </div>
                        
                  <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="number" class="form-control" name="billing_mobile" id="billing_mobile" min-length="10" max-length="10" placeholder="Enter Mobile Number" required="" autofocus="">
                        </div>
                        
                         <div class="form-group">
                            <label>Payment Amount</label>
                            <input type="text" class="form-control" name="payAmount" id="payAmount" value="100" placeholder="Enter Amount" required="" autofocus="">
                        </div>
						
	<!-- submit button -->
	<button  id="PayNow" class="btn btn-success btn-lg btn-block" >Submit & Pay</button>
                       
                    </div>
                </div>
            </div>
</div>
</div>


<script>
    //Pay Amount
    jQuery(document).ready(function($){

jQuery('#PayNow').click(function(e){

	var paymentOption='';
let billing_name = $('#billing_name').val();
	let billing_mobile = $('#billing_mobile').val();
	let billing_email = $('#billing_email').val();
  var shipping_name = $('#billing_name').val();
	var shipping_mobile = $('#billing_mobile').val();
	var shipping_email = $('#billing_email').val();
var paymentOption= "netbanking";
var payAmount = $('#payAmount').val();
			
var request_url="submitpayment.php";
		var formData = {
			billing_name:billing_name,
			billing_mobile:billing_mobile,
			billing_email:billing_email,
			shipping_name:shipping_name,
			shipping_mobile:shipping_mobile,
			shipping_email:shipping_email,
			paymentOption:paymentOption,
			payAmount:payAmount,
			action:'payOrder'
		}
		
		$.ajax({
			type: 'POST',
			url:request_url,
			data:formData,
			dataType: 'json',
			encode:true,
		}).done(function(data){
		
		if(data.res=='success'){
				var orderID=data.order_number;
				var orderNumber=data.order_number;
				var options = {
    "key": data.razorpay_key, // Enter the Key ID generated from the Dashboard
    "amount": data.userData.amount, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "INR",
    "name": "Tutorialswebsite", //your business name
    "description": data.userData.description,
    "image": "https://www.tutorialswebsite.com/wp-content/uploads/2022/02/cropped-logo-tw.png",
    "order_id": data.userData.rpay_order_id, //This is a sample Order ID. Pass 
    "handler": function (response){

    window.location.replace("http://localhost/MysticWaves/inc/Razorpay/payment-success.php?oid="+orderID+"&rp_payment_id="+response.razorpay_payment_id+"&rp_signature="+response.razorpay_signature);

    },
    "modal": {
    "ondismiss": function(){
         window.location.replace("http://localhost/MysticWaves/inc/Razorpay/payment-success.php?oid="+orderID);
     }
},
    "prefill": { //We recommend using the prefill parameter to auto-fill customer's contact information especially their phone number
        "name": data.userData.name, //your customer's name
        "email": data.userData.email,
        "contact": data.userData.mobile //Provide the customer's phone number for better conversion rates 
    },
    "notes": {
        "address": "Tutorialswebsite"
    },
    "config": {
    "display": {
      "blocks": {
        "banks": {
          "name": 'Pay using '+paymentOption,
          "instruments": [
           
            {
                "method": paymentOption
            },
            ],
        },
      },
      "sequence": ['block.banks'],
      "preferences": {
        "show_default_blocks": true,
      },
    },
  },
    "theme": {
        "color": "#3399cc"
    }
};
var rzp1 = new Razorpay(options);
rzp1.on('payment.failed', function (response){

    window.location.replace("http://localhost/MysticWaves/inc/Razorpay/payment-failed.php?oid="+orderID+"&reason="+response.error.description+"&paymentid="+response.error.metadata.payment_id);

         });
      rzp1.open();
     e.preventDefault(); 
}
 
});
 });
    });
</script>


</body>
</html>
