<?php

// Hooks into WOE custom export functions to override
// Format - CSV

require_once 'parsecsv/parsecsv.lib.php';
//logging stuff
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

  //TODO: set role for who can access the Reports
  // add_action( '_admin_menu', function() {
  //   $user = get_role( 'editor' );
  //   if ( ! $user->has_cap( 'view_woocommerce_reports' ) ) {
  //       $user->add_cap( 'view_woocommerce_reports' );
  //   }
  // });

  //hook into custom_output function
  //add_filter("woe_csv_custom_output_func",array($this,"convert_csv"),10,6);
  //add_action("woe_formatter_csv_finished",array($this,"convert_csv",10,1);
  // add_action( "woe_formatter_finish", function($formatter) {
  //   write_log("Finished formating: $formatter->filename");
  //   $exported_contents = file_get_contents($formatter->filename); // Or use fopen+fgetcsv+fclose  for reading
  // // generate new contents
  //   file_put_contents($formatter->filename,$new_contents);  // Or use fopen+fputcsv+fclose
  //   die($formatter->filename);
  // } );
  add_action( "woe_csv_print_footer", function($handle, $formatter) {
    global $OSCWOE_VERSION;
    /**
     * Constants
     */
     // TODO: Move these fields to the Admin Panel
    $retHSTrate = 0.052;
    $retHSTmemo = "Retainable HST on Sales 5.2%";

    $HSTrate = 0.078;
    $HSTmemo = "HST 7.8%";
    $HSTItem = "HST (5) On Sales";
    $HSTAccount = "HST LIAB";

    $qty = -1; //this needs to be -1 for Quickbooks
    //******* END COnstants

    write_log('WOE-OSC ' .$OSCWOE_VERSION .' LOG START');
    write_log("Parsing file: $formatter->filename");
    $csv = new parseCSV($formatter->filename);
    write_log("Parsing fh into csv");
    //write_log($csv);
    $upload   = wp_upload_dir();
    // write_log("Upload dir: ");
    // write_log($upload);
    $upload_dir = $upload['basedir'];
    write_log("Upload basedir: $upload_dir");
    $upload_dir = $upload_dir . '/woo-acct-fields';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }
    //$outputfile = generateRandomString().".csv";
    $newhandle = $upload_dir. "/aparsedfile.csv"; //generateRandomString().".csv";
    write_log("Generating new CSV file: ".$newhandle);

    $data = $csv->data;
    write_log("Reading csv data");
    //write_log($data);

    //Creating template for new CSV output file
    $newcsv = new parseCSV();
    $newfields = ['New Transaction', 'Date',
                    'Type','Num','Payment Method',
                    'Name','Memo','Item','Account',
                    'Class','Qty','Sales Price', 'Amount'];
    $newcsv->fields = $newfields;
    $newcsv->parse($newhandle);
    $newcsv->save($newhandle);
    write_log("Reading newly created parse csv data");
    //write_log($newcsv);
    foreach ($data as $key => $row) {

      //Strip spaces from key names
      $keys = str_replace( ' ', '', array_keys($row) );
      $results = array_combine( $keys, array_values( $row ) );

      $order = (object) $results;
      // write_log( "($order->Num):\t");
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

      $newcsv->save($newhandle,array($line1->get_csvrow()),true);
      // write_log("Reading csv line1");
      // write_log($newcsv->data);
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

      $newcsv->save($newhandle,array($lineProduct->get_csvrow()),true);
      // write_log("Reading csv line2");
      // write_log($newcsv->data);
      // //Third line - Retained HST
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

        $newcsv->save($newhandle,array($lineRetainHST->get_csvrow()),true);
        // write_log("Reading csv line3");
        // write_log($newcsv->data);
        // //fourth line - HST
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

        $newcsv->save($newhandle,array($lineHST->get_csvrow()),true);
        write_log("Reading csv line4");
        //write_log($newcsv->data);
      }
    }

    //save csv as new csv file
    //echo "OSCWOE $OSCWOE_VERSION - Saving New Web Parsed-Orders: $newhandle\n";
    write_log("Final Parsed CSV");
    //write_log($newcsv);


    write_log("writing Parsed CSV into WOE handle: ". $formatter->filename);
    $newcsv->save($formatter->filename);
    //code to check for new content of $formatter
    $postcsv = new parseCSV($formatter->filename);
    write_log("Parsing updated formatter into postcsv");
    write_log("Reading Parsed CSV");
    //write_log($newcsv->data);
    write_log('WOE-OSC ' .$OSCWOE_VERSION .' LOG END');
  }, 10, 2);


  function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }


/**
 * Define CSv Structure of Exported File
 */
class CSVLine {
  public $NewTransaction;
  public $Date;
  public $Type;
  public $Num;
  public $PaymentMethod;
  public $Name;
  public $Memo;
  public $Item;
  public $Account;
  public $Class;
  public $Qty;
  public $SalesPrice;
  public $Amount;
  var $fields = ['New Transaction', 'Date',
                  'Type','Num','Payment Method',
                  'Name','Memo','Item','Account',
                  'Class','Qty','Sales Price', 'Amount'];
  function get_fields() {
			return $this->fields;
		}
    function get_csvrow(){
       $row = [$this->NewTransaction,
                $this->Date,
                $this->Type,
                $this->Num,
                $this->PaymentMethod,
                $this->Name,
                $this->Memo,
                $this->Item,
                $this->Account,
                $this->Class,
                $this->Qty,
                $this->SalesPrice,
                $this->Amount];
      return $row;
    }
}
