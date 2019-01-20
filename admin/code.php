<?php

add_action( "woe_csv_print_footer", function($handle, $formatter) {
  $csv = new parseCSV($handle);
  //$csv = new parseCSV('orders.csv');
  $newhandle = array[];
  // cycle through lines
  //put each line into new array
  foreach ($csv->titles as $value) {
    echo "$value\t" ;
  }
  echo "\n";

  $data = $csv->data;

  //Creating template for new CSV output file
  $newcsv = new parseCSV();
  $newfields = ['New Transaction', 'Date',
                  'Type','Num','Payment Method',
                  'Name','Memo','Item','Account',
                  'Class','Qty','Sales Price', 'Amount'];
  $newcsv->fields = $newfields;
  $newcsv->parse($newhandle);
  $newcsv->save($newhandle);


  // $csvrows = "";
  foreach ($data as $key => $row) {
    echo "[$key]:\t";

    //Strip spaces from key names
    $keys = str_replace( ' ', '', array_keys($row) );
    $results = array_combine( $keys, array_values( $row ) );
    // print_r($results);

    $order = (object) $results;
    echo "($order->Num):\t";
    $name = "$order->LastName, $order->FirstName";

    //Process First Line
    $line1 = new CSVLine();
    $line1->NewTransaction = "Yes";
    $line1->Num = $order->Num;
    $line1->Date = $order->Date;
    $line1->Type = $order->Type;
    $line1->PaymentMethod = $order->PaymentMethod;
    $line1->Name = $name;
    $line1->Memo = "Order# $order->Num";
    $line1->Item = "";
    $line1->Account = "*Undeposited Funds";
    $line1->Class = $order->Class;
    $line1->SalesPrice = "";
    $line1->Qty = $qty;
    $line1->Amount = $order->Amount;
    echo "$line1->NewTransaction\t";
    echo "$line1->Date\t";
    echo "$line1->Type\t";
    echo "$line1->Num\t";
    echo "$line1->PaymentMethod\t";
    echo "$line1->Name\t";
    echo "$line1->Num\t";
    echo "$line1->Memo\t";
    echo "$line1->Item\t";
    echo "$line1->Account\t";
    echo "$line1->Class\t";
    echo "$line1->Qty\t";
    echo "$line1->SalesPrice\t";
    echo "$line1->Amount\t";
    echo "\n";
    $newcsv->save($newhandle,array($line1->get_csvrow()),true);

    //Second line - Product
    $lineProduct = new CSVLine();
    $lineProduct->NewTransaction = "";
    $lineProduct->Num = "";
    $lineProduct->Date = "";
    $lineProduct->Type = "";
    $lineProduct->PaymentMethod = "";
    $lineProduct->Name = $name;
    $lineProduct->Memo = $order->Memo;
    $lineProduct->Item = $order->Item;
    $lineProduct->Account = $order->Account;
    $lineProduct->Class = $order->Class;
    $lineProduct->SalesPrice = $order->SalesPrice;
    $lineProduct->Qty = -$order->Quantity;
    $lineProduct->Amount = $order->SalesPrice * -$order->Quantity ;
    echo "$lineProduct->NewTransaction\t";
    echo "$lineProduct->Date\t";
    echo "$lineProduct->Type\t";
    echo "$lineProduct->Num\t";
    echo "$lineProduct->PaymentMethod\t";
    echo "$lineProduct->Name\t";
    echo "$lineProduct->Num\t";
    echo "$lineProduct->Memo\t";
    echo "$lineProduct->Item\t";
    echo "$lineProduct->Account\t";
    echo "$lineProduct->Class\t";
    echo "$lineProduct->Qty\t";
    echo "$lineProduct->SalesPrice\t";
    echo "$lineProduct->Amount\t";
    echo "\n";
    $newcsv->save($newhandle,array($lineProduct->get_csvrow()),true);

    //Third line - Retained HST
    if($order->OrderTaxAmount){
      $lineRetainHST = new CSVLine();
      $lineRetainHST->NewTransaction = "";
      $lineRetainHST->Num = "";
      $lineRetainHST->Date = "";
      $lineRetainHST->Type = "";
      $lineRetainHST->PaymentMethod = "";
      $lineRetainHST->Name = $name;
      $lineRetainHST->Memo = $retHSTmemo;
      $lineRetainHST->Item = $order->Item . "-RET HST";
      $lineRetainHST->Account = $order->Account;
      $lineRetainHST->Class = $order->Class;
      $lineRetainHST->SalesPrice = round($order->SalesPrice * $retHSTrate, 2);
      $lineRetainHST->Qty = -$order->Quantity;
      $lineRetainHST->Amount = $lineRetainHST->SalesPrice * -$order->Quantity;
      echo "$lineRetainHST->NewTransaction\t";
      echo "$lineRetainHST->Date\t";
      echo "$lineRetainHST->Type\t";
      echo "$lineRetainHST->Num\t";
      echo "$lineRetainHST->PaymentMethod\t";
      echo "$lineRetainHST->Name\t";
      echo "$lineRetainHST->Num\t";
      echo "$lineRetainHST->Memo\t";
      echo "$lineRetainHST->Item\t";
      echo "$lineRetainHST->Account\t";
      echo "$lineRetainHST->Class\t";
      echo "$lineRetainHST->Qty\t";
      echo "$lineRetainHST->SalesPrice\t";
      echo "$lineRetainHST->Amount\t";
      echo "\n";
      $newcsv->save($newhandle,array($lineRetainHST->get_csvrow()),true);

      //fourth line - HST
      $lineHST = new CSVLine();
      $lineHST->NewTransaction = "";
      $lineHST->Num = "";
      $lineHST->Date = "";
      $lineHST->Type = "";
      $lineHST->PaymentMethod = "";
      $lineHST->Name = $name;
      $lineHST->Memo = $HSTmemo;
      $lineHST->Item = $HSTItem;
      $lineHST->Account = $HSTAccount;
      $lineHST->Class = $order->Class;
      $lineHST->SalesPrice = round($order->SalesPrice * $HSTrate,2);
      $lineHST->Qty = -$order->Quantity;
      $lineHST->Amount = $lineHST->SalesPrice * -$order->Quantity;
      echo "$lineHST->NewTransaction\t";
      echo "$lineHST->Date\t";
      echo "$lineHST->Type\t";
      echo "$lineHST->Num\t";
      echo "$lineHST->PaymentMethod\t";
      echo "$lineHST->Name\t";
      echo "$lineHST->Num\t";
      echo "$lineHST->Memo\t";
      echo "$lineHST->Item\t";
      echo "$lineHST->Account\t";
      echo "$lineHST->Class\t";
      echo "$lineHST->Qty\t";
      echo "$lineHST->SalesPrice\t";
      echo "$lineHST->Amount\t";
      echo "\n";
      $newcsv->save($newhandle,array($lineHST->get_csvrow()),true);

    }
  }

  //save csv as new csv file
  echo "Saving New Parsed-Orders\n";
  $handle = $newhandle;

}, 10, 2);
//add_action("woe_custom_export_to_email",array($this,"send_csv_files"),10,4);








}


$fh1 = fgets($handle, $fs['size']);
write_log("Reading fh1 and size is " . $fs['size']);
write_log($fh1);
$fh = fgetcsv($handle);
write_log("Reading fh fgetcsv and size is " . $fs['size']);
write_log($fh);
