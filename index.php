<?php 
    // 	file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
	//decare 

//Channel access token
    $accessToken = "XYVLzamO4/q+M3MWnhz54yEBMuL4x/s8UW92LNpHayN13J3tBOQIbKyiBNYWROBIEVxyxiKbJHZB5SmX+sJg7S/ybA41S9t/pUHgwKA5jOkE//mGylQdYwNPJ+ioVNCNJQiVb8dOJapS+Qg0eBoGmQdB04t89/1O/w1cDnyilFU=";//
    
    $content = file_get_contents('php://input');
    $arrayJson = json_decode($content, true);
    
    $arrayHeader = array();
    $arrayHeader[] = "Content-Type: application/json";
    $arrayHeader[] = "Authorization: Bearer {$accessToken}";
    
    //รับข้อความจากผู้ใช้
    $message = $arrayJson['events'][0]['message']['text'];
    //เปลี่ยนข้อมูลจากผู้ใช้เป็นพิมพ์ใหญ่ทั้งหมด
	$message =  strtoupper($message);
	
    //   file_put_contents('log.txt',  getThaiEmsTrac($message) . PHP_EOL, FILE_APPEND);

    #กรณี เป็นเลข EMS
    if(preg_match('/^[A-Za-z]{2}[0-9]+TH$/',$message)&&strlen($message)==13)
    {
    $emsDetail = getThaiEmsTrack($message); //รับค่าจาก api thai track จากรหัส ems ที่รับมา
        file_put_contents('logEMS.txt', $emsDetail);

   // file_put_contents('log.txt', $emsDetail . PHP_EOL, FILE_APPEND);
    $emsDetail = json_decode($emsDetail,true);
    $ems_track = $emsDetail['response']['items'][$message]; //เข้าไปยัง array ของ item ems'
      if(!empty($ems_track)){
    $ems_track_latest = $ems_track[count($ems_track)-1];
    
     //file_put_contents('logEMS.txt', $ems_track);
    
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        // $emsDetail = json_decode($emsDetail,true);
    $arrayPostData['messages'][0] = getBubbleMessage($ems_track_latest);
    //เช็คสถานะการจัดส่ง เพื่อส่งสติกเกอร์
   $statusEms = $ems_track_latest['status'];
   if($statusEms=='501'||$statusEms=='302'){
       $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "11537";
        $arrayPostData['messages'][1]['stickerId'] = "52002745";
   }
    else if($statusEms=='401'){ //นำจ่ายไม่สำเร็จ
       $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "11538";
        $arrayPostData['messages'][1]['stickerId'] = "51626522";
   }
    else if($statusEms=='201'||$statusEms=='301'){ //ระหว่างขนส่ง
       $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "11539";
        $arrayPostData['messages'][1]['stickerId'] = "52114146";
   }
        
    }
    else{
         $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "รหัสพัสดุไม่ถูกจ้าา";  $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "11537";
        $arrayPostData['messages'][1]['stickerId'] = "52002770";
    }
    
            replyMsg($arrayHeader,$arrayPostData);

   }
	
   #ตัวอย่าง Message Type "Text"
   else if($message == "สวัสดี"||$message == "ไง"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "สวัสดีจ้าาา";
        replyMsg($arrayHeader,$arrayPostData);
    }
    #ตัวอย่าง Message Type "Sticker"
    else if($message == "ฝันดี"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "sticker";
        $arrayPostData['messages'][0]['packageId'] = "2";
        $arrayPostData['messages'][0]['stickerId'] = "46";
        replyMsg($arrayHeader,$arrayPostData);
    }
    #ตัวอย่าง Message Type "Image"
    else if($message == "แมว"){
        $image_url = "https://i.pinimg.com/originals/cc/22/d1/cc22d10d9096e70fe3dbe3be2630182b.jpg";
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "image";
        $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
        $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
        replyMsg($arrayHeader,$arrayPostData);
    }
    #ตัวอย่าง Message Type "Location"
    else if($message == "พิกัดสยามพารากอน"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "location";
        $arrayPostData['messages'][0]['title'] = "สยามพารากอน";
        $arrayPostData['messages'][0]['address'] =   "13.7465354,100.532752";
        $arrayPostData['messages'][0]['latitude'] = "13.7465354";
        $arrayPostData['messages'][0]['longitude'] = "100.532752";
        replyMsg($arrayHeader,$arrayPostData);
    }
    #ตัวอย่าง Message Type "Text + Sticker ใน 1 ครั้ง"
    else if($message == "ลาก่อน"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "จะไปแล้วหรออ ~";        $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "1";
        $arrayPostData['messages'][1]['stickerId'] = "131";
        replyMsg($arrayHeader,$arrayPostData);
    }
    else if($message=="TEST"){
    $emsDetail = getThaiEmsTrack('ED967523278TH'); //รับค่าจาก api thai track จากรหัส ems ที่รับมา
   // file_put_contents('log.txt', $emsDetail . PHP_EOL, FILE_APPEND);
    $emsDetail = json_decode($emsDetail,true);
    $ems_track = $emsDetail['response']['items']['ED967523278TH']; //เข้าไปยัง array ของ item ems'
    $ems_track_latest = $ems_track[count($ems_track)-1];
    
    file_put_contents('log.txt', print_r($ems_track_latest, true));
         $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        // $emsDetail = json_decode($emsDetail,true);
         $arrayPostData['messages'][0] = getBubbleMessage($ems_track_latest);
        //  $arrayPostData['messages'][0]['type'] = "text";
        //  $arrayPostData['messages'][0]['text'] = "สวัสดีจ้าาา";
        replyMsg($arrayHeader,$arrayPostData);
    }
    else{
              $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "ผมไม่เข้าใจสิ่งที่คุณพูด"; $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "11537";
        $arrayPostData['messages'][1]['stickerId'] = "52002744";
        replyMsg($arrayHeader,$arrayPostData);
    }
    

    
  function getBubbleMessage($emsLatest){
      #ส่วนของค่าที่ได้มาจาก api thai ems track
      $trackID = $emsLatest['barcode'];
      $status = $emsLatest['status'];
      $status_descript = $emsLatest['status_description'];
      $dateTime = $emsLatest['status_date'];
      //แปลงformat วันเวลา
      $dateData = substr($dateTime,0,19);
      $date = substr($dateData,0,10);
      $time = substr($dateData,11,25);
      $dateTime = $time.' '.$date;
      //
      $place = $emsLatest['location'];
      $delivery_descript = $emsLatest['delivery_description']; 
      $signature = $emsLatest['signature']; 
      $emsTrack_arr =[];
       #เป็นกรณีนำจ่ายแล้ว
     if($status=='401'||$status=='501'){ //Ei044360356th
      $emsTrack_arr = getTemplateBubbleArray_Delivery();
         ///ถ้ามีลิ้งให้มีข้อความลิ้ง
         if(!empty($signature)){
             $emsTrack_arr['contents']['body']['contents'][1]['contents'][5]['contents'][1]['text'] = "ลายเซ็นผู้รับ";
           #ems uri signature
            $emsTrack_arr['contents']['body']['contents'][1]['contents'][5]['contents'][1]['action']['uri'] = $signature;
             
         }
         ///ถ้าไม่มีลิ้ง ให้แสดงเป็นค่าว่าง
         else{
             
              $emsTrack_arr['contents']['body']['contents'][1]['contents'][5]['contents'][1]['text'] = "-";
              #ems uri signature
               unset($emsTrack_arr['contents']['body']['contents'][1]['contents'][5]['contents'][1]['action']);
                
                

         }
     //เข้าถึงส่วนแสดงข้อมูลจาก api ใน bubble
    //  $emsTrack_arr['contents']['body']['contents'][1]['contents'];
     #ems tag
    $emsTrack_arr['contents']['body']['contents'][1]['contents'][0]['contents'][1]['text'] =  $trackID;//
     #ems status
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][1]['contents'][1]['text'] = $status_descript;
     #ems date time
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][2]['contents'][1]['text'] = $dateTime;
     #ems place
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][3]['contents'][1]['text'] = $place;//"กองทัพบก";
     #ems delivery descript
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][4]['contents'][1]['text'] = $delivery_descript;
   
    }
    #เป็นกรณียังไม่นำจ่าย
    else{
      //รับ template bubble อาเรย์
     $emsTrack_arr = getTemplateBubbleArray();
     //เข้าถึงส่วนแสดงข้อมูลจาก api ใน bubble
    //  $emsTrack_arr['contents']['body']['contents'][1]['contents'];
     #ems tag
    $emsTrack_arr['contents']['body']['contents'][1]['contents'][0]['contents'][1]['text'] =  $trackID;//
     #ems status
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][1]['contents'][1]['text'] = $status_descript;
     #ems date time
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][2]['contents'][1]['text'] = $dateTime;
     #ems place
     $emsTrack_arr['contents']['body']['contents'][1]['contents'][3]['contents'][1]['text'] = $place;//"กองทัพบก";
    }
                   file_put_contents('log.txt', print_r($emsTrack_arr, true));


     return $emsTrack_arr;
  }
  function getTemplateBubbleArray(){
      $emsTrack_json = '{ "type": "flex", "altText": "Flex Message", "contents": { "type": "bubble", "direction": "ltr", "hero": { "type": "image", "url": "https://imodtoy.com/wp-content/uploads/2019/09/Thailand-Post_Cover-1200x628.jpg", "size": "full", "aspectRatio": "16:9", "aspectMode": "cover", "backgroundColor": "#FFFFFF" }, "body": { "type": "box", "layout": "vertical", "spacing": "md", "contents": [ { "type": "text", "text": "เช็คเลขพัสดุ", "size": "xl", "align": "center", "gravity": "center", "weight": "bold", "color": "#DF4C4C", "wrap": true }, { "type": "box", "layout": "vertical", "spacing": "sm", "margin": "lg", "contents": [ { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Track", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "ED967523278TH", "flex": 4, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Status", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "อยู่ระหว่างการขนส่ง", "flex": 4, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Date", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "Monday 25, 9:00PM", "flex": 4, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Place", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "กองทัพอากาศ", "flex": 4, "size": "sm", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "vertical", "margin": "xxl", "contents": [ { "type": "spacer" } ] } ] } ] } } }';
      return json_decode($emsTrack_json,true);
  }
   #bubble กรณีของถูกดำเนินการจัดส่งแล้ว
    function getTemplateBubbleArray_Delivery(){
        $emsTrack_json = '{ "type": "flex", "altText": "Flex Message", "contents": { "type": "bubble", "direction": "ltr", "hero": { "type": "image", "url": "https://imodtoy.com/wp-content/uploads/2019/09/Thailand-Post_Cover-1200x628.jpg", "size": "full", "aspectRatio": "16:9", "aspectMode": "cover", "backgroundColor": "#FFFFFF" }, "body": { "type": "box", "layout": "vertical", "spacing": "md", "contents": [ { "type": "text", "text": "เช็คเลขพัสดุ", "size": "xl", "align": "center", "gravity": "center", "weight": "bold", "color": "#DF4C4C", "wrap": true }, { "type": "box", "layout": "vertical", "spacing": "sm", "margin": "lg", "contents": [ { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Track", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "ED967523278TH", "flex": 3, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Status", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "นำจ่ายสำเร็จ", "flex": 3, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Date", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "Monday 25, 9:00PM", "flex": 3, "size": "md", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "sm", "contents": [ { "type": "text", "text": "Place", "flex": 1, "size": "md", "color": "#AAAAAA" }, { "type": "text", "text": "กองทัพอากาศ", "flex": 3, "size": "sm", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "lg", "contents": [ { "type": "text", "text": "Delivery", "flex": 0, "size": "sm", "color": "#AAAAAA" }, { "type": "text", "text": "ผู้รับได้รับสิ่งของเรียบร้อยแล้ว", "flex": 0, "size": "sm", "color": "#666666", "wrap": true } ] }, { "type": "box", "layout": "baseline", "spacing": "lg", "contents": [ { "type": "text", "text": "Signature", "flex": 0, "size": "sm", "color": "#AAAAAA" }, { "type": "text", "text": "ลายเซ็นผู้รับ", "flex": 4, "size": "sm", "align": "start", "weight": "bold", "color": "#3F69F0", "action": { "type": "uri", "uri": "https://track.thailandpost.co.th/signature/QDIzMjc4YjVzMGx1VDMz/QGI1c0VEMGx1VDMx/QGI1czBsVEh1VDM0/QGI1czBsdTk2NzVUMzI=" }, "wrap": true } ] }, { "type": "spacer" } ] } ] } } }';
         return json_decode($emsTrack_json,true);
        
    }
  
  function getThaiEmsTrack($ems_trac){ //เรียก api ems track
    $url = 'https://trackapi.thailandpost.co.th/post/api/v1/track';
    $ch = curl_init($url);
    $datas = ['status'=>'all', 'language'=>'TH', "barcode"=>array ($ems_trac)];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datas));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJzZWN1cmUtYXBpIiwiYXVkIjoic2VjdXJlLWFwcCIsInN1YiI6IkF1dGhvcml6YXRpb24iLCJleHAiOjE1NzQ0MzU4NzksInJvbCI6WyJST0xFX1VTRVIiXSwiZCpzaWciOnsicCI6InpXNzB4IiwicyI6bnVsbCwidSI6IjdhZDc5NzViYzNhYzQzODY1OTM0NzE3NWFkZDA5OGE4IiwiZiI6InhzeiM5In19.FntzXVTBXbj-FDWCsd97uyimHSJk3LRhVz4HpkWQ3wSL4TZA_Zkf18rZ98ZEIgO3GXlKU60YZL4IwowelP3UTA',
            'Content-Type: application/json'
        ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
    //echo curl_exex;
    curl_close($ch);
}
  
  
  function replyMsg($arrayHeader,$arrayPostData){
        $strUrl = "https://api.line.me/v2/bot/message/reply";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$strUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);    
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arrayPostData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close ($ch);
    }
exit;
  
  ?>


	