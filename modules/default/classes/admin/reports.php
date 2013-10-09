<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_reports
{
	#CONSTRUCTOR
	function c_reports()
	{
		$this->libFunc=new c_libFunctions();
		$this->pageTplPath=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="30";
	}


	function m_showBestSellerReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("bSeller",$this->bSellerTemplate);
		
		$this->ObTpl->set_block("bSeller","TPL_BESTSELLERS_BLK","bestSellers_blk");
		$this->ObTpl->set_var("bestSellers_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
        $this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		#Getting current product ID's 
		$this->obDb->query = "SELECT * FROM ".PRODUCTS;
		$rowProductId = $this->obDb->fetchQuery();
		$rowIdCount = $this->obDb->record_count;
	if ($rowIdCount>0){
		$id_rows = array();
        
		for ($i=0; $i<$rowIdCount; $i++ )
        {
           $id_rows[$i] = $rowProductId[$i]->iProdid_PK;
        }

		
		#QUERY TO GET TOP TEN PRODUCTS
		$this->obDb->query = "SELECT iProductid_FK, SUM(iQty) as top_10,seo_title,fPrice,vTitle FROM ".ORDERPRODUCTS." WHERE iProductid_FK IN (" . implode(",", $id_rows). ")
 		GROUP BY iProductid_FK ORDER BY top_10 DESC";
		$rowTop10 = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if($rowCount >0)
		{
			for ($i=0;$i<$rowCount;$i++)
			{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowTop10[$i]->seo_title;
				$this->ObTpl->set_var("TPL_VAR_TOPTENPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERTITLE",$this->libFunc->m_displayContent($rowTop10[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERCOUNT",$rowTop10[$i]->top_10);	
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERPRICE",$rowTop10[$i]->fPrice);	
				$this->ObTpl->parse("bestSellers_blk","TPL_BESTSELLERS_BLK",true);
			}
			
		}
	}	
		return($this->ObTpl->parse("return","bSeller"));
	}
	
	function m_showOutOfStockReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("outOf",$this->outOfStockTemplate);
		$this->ObTpl->set_block("outOf","TPL_OUTOFSTOCK_BLK","outOfStock_blk");
		$this->ObTpl->set_var("outOfStock_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iUseinventory = 1 AND iInventory <= 0 ";
		$OutOfStock = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		for($j=0;$j<$rowCount;$j++)
		{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$OutOfStock[$j]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_OUTOFPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_OUTOFTITLE",$this->libFunc->m_displayContent($OutOfStock[$j]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_OUTOFINVENTORY",$OutOfStock[$j]->iInventory);			
			$this->ObTpl->parse("outOfStock_blk","TPL_OUTOFSTOCK_BLK",true);
		} //ef
		
		return($this->ObTpl->parse("return","outOf"));
	}
	
	function m_showlowStockReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("lowStock",$this->lowStockStockTemplate);
		$this->ObTpl->set_block("lowStock","TPL_LOWSTOCK_BLK","lowStock_blk");
		$this->ObTpl->set_var("lowStock_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iUseinventory = 1 AND iInventory <= iInventoryMinimum";
		$LowStock = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		
		for($k=0;$k<$rowCount;$k++){
			
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$LowStock[$k]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_LOWPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_LOWOFTITLE",$this->libFunc->m_displayContent($LowStock[$k]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_MINIMUM",$LowStock[$k]->iInventoryMinimum);
				$this->ObTpl->set_var("TPL_VAR_LOWOFINVENTORY",$LowStock[$k]->iInventory);
			$this->ObTpl->parse("lowStock_blk","TPL_LOWSTOCK_BLK",true);	
		}
		return($this->ObTpl->parse("return","lowStock"));
		
	}
	
	function m_showTopSearchReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("topSearch",$this->topSearchTemplate);
		$this->ObTpl->set_block("topSearch","TPL_TOPSEARCHES_BLK","topsearches_blk");
		$this->ObTpl->set_var("topsearches_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".SEARCHES." ORDER BY iNumberOfSearches DESC" ;
		$TopSearches = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount>0){
		for($i=0;$i<$rowCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_TOPSEARCHTITLE",$this->libFunc->m_displayContent($TopSearches[$i]->vSearchTerm));
			$this->ObTpl->set_var("TPL_VAR_TOPSEARCHCOUNT",$this->libFunc->m_displayContent($TopSearches[$i]->iNumberOfSearches));
			$this->ObTpl->set_var("TPL_VAR_RECORDSFOUND",$this->libFunc->m_displayContent($TopSearches[$i]->iRecFoud));
			
			$this->ObTpl->parse("topsearches_blk","TPL_TOPSEARCHES_BLK",true);
			
		}
		}
		return($this->ObTpl->parse("return","topSearch"));
	}
	
	function m_showMostViewedReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("mostViewed",$this->mostViewedTemplate);
		$this->ObTpl->set_block("mostViewed","TPL_MOSTVIEWED_BLK","mostviewed_blk");
		$this->ObTpl->set_var("mostviewed_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iViewCount > 0 ORDER BY iViewCount DESC" ;
		$MostViewed = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		
		for($i=0;$i<$rowCount;$i++)
		{
			
			$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$MostViewed[$i]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_MOSTPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
			$this->ObTpl->set_var("TPL_VAR_MOSTTITLE",$this->libFunc->m_displayContent($MostViewed[$i]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_VIEWS",$this->libFunc->m_displayContent($MostViewed[$i]->iViewCount));
			$this->ObTpl->set_var("TPL_VAR_MOSTPRICE",CONST_CURRENCY.$this->libFunc->m_displayContent($MostViewed[$i]->fPrice));
			
			$this->ObTpl->parse("mostviewed_blk","TPL_MOSTVIEWED_BLK",true);
			
		}
	
		return($this->ObTpl->parse("return","mostViewed"));
	}
	
	function m_showAbandonmentReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("abandonment",$this->abandonmentTemplate);
		$this->ObTpl->set_block("abandonment","TPL_ABANDONMENT_BLK","abandonment_blk");
		$this->ObTpl->set_var("abandonment_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iAddCount > 0 ORDER BY iAddCount DESC" ;
		$Abandonment = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		
		for($i=0;$i<$rowCount;$i++)
		{
			$this->obDb->query = "SELECT iProductid_FK, COUNT(*) as top_10 FROM ".ORDERPRODUCTS." WHERE iProductid_FK ='".$Abandonment[$i]->iProdid_PK."' GROUP BY iProductid_FK";
			$TotalPurchased = $this->obDb->fetchQuery();
			
			$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$Abandonment[$i]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_ABANDONPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
			$this->ObTpl->set_var("TPL_VAR_ABANDONTITLE",$this->libFunc->m_displayContent($Abandonment[$i]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_TOTALADDED",$this->libFunc->m_displayContent($Abandonment[$i]->iAddCount));
			$this->ObTpl->set_var("TPL_VAR_TOTALPURCHASED",$TotalPurchased[0]->top_10);
			
			$this->ObTpl->parse("abandonment_blk","TPL_ABANDONMENT_BLK",true);
			
		}
	
		return($this->ObTpl->parse("return","abandonment"));
	}
	
	function m_showNewOrderReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("newOrder",$this->newOrderTemplate);
		$this->ObTpl->set_block("newOrder","TPL_NEWORDER_BLK","newOrder_blk");
		$this->ObTpl->set_var("newOrder_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		##START NEW ORDER BLOCK
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vStatus = 'New' ORDER BY tmOrderDate DESC";
		$NewOrders = $this->obDb->fetchQuery();
		$NewOrdersCount = $this->obDb->record_count;
		
		if($NewOrdersCount >0){	
			
			for($l=0;$l<$NewOrdersCount;$l++)
			{
			
				$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE vEmail = '".$NewOrders[$l]->vEmail."'";
				$CheckCust = $this->obDb->fetchQuery();
				$CheckCustCount = $this->obDb->record_count;
				
				if($CheckCustCount >0)
				{
					$CustUrl=SITE_URL."user/adminindex.php?action=user.details&amp;id=".$NewOrders[$l]->iCustomerid_FK;
					
					//<a href="{TPL_VAR_SITEURL}user/adminindex.php?action=user.details&amp;id={TPL_VAR_ID}" class="linkPreview">View</a>
					
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","<a href=\"".$CustUrl."\">");
					$this->ObTpl->set_var("TPL_VAR_NEWORDERCUSTOMER",$NewOrders[$l]->vFirstName." ".$NewOrders[$l]->vLastName);
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","</a>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","");
					$this->ObTpl->set_var("TPL_VAR_NEWORDERCUSTOMER","Unregistered - see invoice");
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","");
				}
				
				$orderUrl=SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$NewOrders[$l]->iInvoice;
				$this->ObTpl->set_var("TPL_VAR_ORDERURL",$orderUrl);
				$this->ObTpl->set_var("TPL_VAR_NEWORDERNUMBER",($NewOrders[$l]->iInvoice));
				$this->ObTpl->set_var("TPL_VAR_ORDERTOTAL",CONST_CURRENCY.$NewOrders[$l]->fTotalPrice);
				
				$this->ObTpl->parse("newOrder_blk","TPL_NEWORDER_BLK",true);
			}	
		}
		return($this->ObTpl->parse("return","newOrder"));
	}
	
	function m_showPendingReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("pending",$this->pendingTemplate);
		$this->ObTpl->set_block("pending","TPL_PENDINGORDER_BLK","pendingOrder_blk");
		$this->ObTpl->set_var("pendingOrder_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		##START PENDING ORDERS BLOCK		
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vStatus = 'Pending' ORDER BY tmOrderDate DESC";
		$PenOrders = $this->obDb->fetchQuery();
		$PenOrdersCount = $this->obDb->record_count;
		
		if($PenOrdersCount >0)
		{
			if ($PenOrdersCount > 5) $PenOrdersCount = 5;
			
			for($m=0;$m<$PenOrdersCount;$m++)	
			{
				$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE vEmail = '".$PenOrders[$m]->vEmail."'";
				$CheckCust = $this->obDb->fetchQuery();
				$CheckCustCount = $this->obDb->record_count;
				
				if($CheckCustCount >0)
				{
					$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$PenOrders[$m]->iCustomerid_FK;
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","<a href=\"".$CustUrl."\">");
					$this->ObTpl->set_var("TPL_VAR_PENORDERCUSTOMER",$PenOrders[$m]->vFirstName." ".$PenOrders[$m]->vLastName);
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","</a>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","");
					$this->ObTpl->set_var("TPL_VAR_PENORDERCUSTOMER","Unregistered - see invoice");
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","");
				}
				
				$orderUrl=SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$PenOrders[$m]->iInvoice;
				$this->ObTpl->set_var("TPL_VAR_PENURL",$orderUrl);
				$this->ObTpl->set_var("TPL_VAR_PENORDERNUMBER",($PenOrders[$m]->iInvoice));
				$this->ObTpl->set_var("TPL_VAR_PENORDERTOTAL",CONST_CURRENCY.$PenOrders[$m]->fTotalPrice);
				
				$this->ObTpl->parse("pendingOrder_blk","TPL_PENDINGORDER_BLK",true);	
			}		
		}		
		return($this->ObTpl->parse("return","pending"));
	}
	
	function m_showIncompleteReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("incomplete",$this->incompleteTemplate);
		$this->ObTpl->set_block("incomplete","TPL_INCOMPLETEORDER_BLK","incomplete_blk");	
		$this->ObTpl->set_var("incomplete_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		## START INCOMPLETE ORDERS BLOCK 
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE iOrderStatus = '1' ORDER BY tmOrderDate DESC";
		$INCOrders = $this->obDb->fetchQuery();
		$INCOrdersCount = $this->obDb->record_count;
			if($INCOrdersCount>0)
			{
				if ($INCOrdersCount > 5) $INCOrdersCount = 5;
				for($m=0;$m<$INCOrdersCount;$m++)	
				{
					$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE vEmail = '".$INCOrders[$m]->vEmail."'";
					$CheckIncCust = $this->obDb->fetchQuery();
					$CheckIncCount = $this->obDb->record_count;	
					
					if($CheckIncCount >0)
					{
						$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$INCOrders[$m]->iCustomerid_FK;
						$this->ObTpl->set_var("TPL_VAR_CUSTURL","<a href=\"".$CustUrl."\">");
						$this->ObTpl->set_var("TPL_VAR_INCCUSTOMER",$INCOrders[$m]->vFirstName." ".$INCOrders[$m]->vLastName);
						$this->ObTpl->set_var("TPL_VAR_CLOSETAG","</a>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_CUSTURL","");
						$this->ObTpl->set_var("TPL_VAR_INCCUSTOMER","Unregistered - see invoice");
						$this->ObTpl->set_var("TPL_VAR_CLOSETAG","");
					}
					
					$orderUrl=SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$INCOrders[$m]->iInvoice;
					$this->ObTpl->set_var("TPL_VAR_INCURL",$orderUrl);
					$this->ObTpl->set_var("TPL_VAR_INCORDERNUMBER",($INCOrders[$m]->iInvoice));
					$this->ObTpl->set_var("TPL_VAR_INCORDERTOTAL",CONST_CURRENCY.$INCOrders[$m]->fTotalPrice);
					
					$this->ObTpl->parse("incomplete_blk","TPL_INCOMPLETEORDER_BLK",true);	
				}	
			}		
		return($this->ObTpl->parse("return","incomplete"));
	}
	
	function m_showNewCustomerReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("newCust",$this->newCustTemplate);
		$this->ObTpl->set_block("newCust","TPL_NEWCUSTOMERS_BLK","newcustomers_blk");
		$this->ObTpl->set_var("newcustomers_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obDb->query = "SELECT * FROM ".CUSTOMERS." ORDER BY tmSignupDate DESC";
		$NewCust = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		for($n=0;$n<$rowCount;$n++)
		{
				$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$NewCust[$n]->iCustmerid_PK;
				$this->ObTpl->set_var("TPL_VAR_CUSTURL",$CustUrl);
                $this->ObTpl->set_var("TPL_VAR_NEWCUSTOMER",$NewCust[$n]->vFirstName." ".$NewCust[$n]->vLastName);	
				$signUpDate = date("d/m/Y",$NewCust[$n]->tmSignupDate);
				$this->ObTpl->set_var("TPL_VAR_SIGNUPDATE",$signUpDate);	
				
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryid_PK =".$NewCust[$n]->vCountry;
				$GetCountry = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_LOCATION",$GetCountry[0]->vCountryName);	
				$this->ObTpl->parse("newcustomers_blk","TPL_NEWCUSTOMERS_BLK",true);
		}		
		return($this->ObTpl->parse("return","newCust"));	
	}
	
	function m_showReturningCustomerReport()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("returnCust",$this->returnCustTemplate);
		$this->ObTpl->set_block("returnCust","TPL_RETCUSTOMERS_BLK","retcustomers_blk");
		$this->ObTpl->set_var("retcustomers_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		##START RETURNING CUSTOMERS BLOCK
		$this->obDb->query = "SELECT vEmail,vFirstName,vLastName, COUNT(vEmail) AS orderCount FROM ".ORDERS." GROUP BY vEmail HAVING COUNT(vEmail)> 1 ORDER BY orderCount DESC"; 	
		$ReturnCust = $this->obDb->fetchQuery();
		$ReturnCount = $this->obDb->record_count;
		
		for($i=0;$i<$ReturnCount;$i++)
		{
			$Total= 0;
			$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vEmail ='".$ReturnCust[$i]->vEmail."'"; 	
			$GetOrders = $this->obDb->fetchQuery();
			$Count = $this->obDb->record_count;

			for($j=0;$j<$Count;$j++)
			{
				$Total = $Total + $GetOrders[$j]->fTotalPrice;
			}
			
			$this->ObTpl->set_var("TPL_VAR_RETCUSTOMER",$ReturnCust[$i]->vFirstName." ".$ReturnCust[$i]->vLastName);	
			$this->ObTpl->set_var("TPL_VAR_NUMORDERS",$Count);	
			$this->ObTpl->set_var("TPL_VAR_TOTAL",CONST_CURRENCY.$Total);
			$this->ObTpl->parse("retcustomers_blk","TPL_RETCUSTOMERS_BLK",true);	
		}
		return($this->ObTpl->parse("return","returnCust"));	
	}
	
	function m_showBestCustomerReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("bestCust",$this->bestCustTemplate);
		$this->ObTpl->set_block("bestCust","TPL_BESTCUSTOMERS_BLK","bestcustomers_blk");
		$this->ObTpl->set_var("bestcustomers_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		##START BESTCUSTOMERS BLOCK
		$this->obDb->query = "SELECT vEmail,vFirstName,vLastName,SUM(fTotalPrice) AS total FROM ".ORDERS." GROUP BY vEmail HAVING COUNT(vEmail)> 1 ORDER BY total DESC"; 	
		$BestCust = $this->obDb->fetchQuery();
		$BestCount = $this->obDb->record_count;
		
		for($i=0;$i<$BestCount;$i++)
		{
			
			$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vEmail ='".$BestCust[$i]->vEmail."'"; 	
			$Orders = $this->obDb->fetchQuery();
			$OrderCount = $this->obDb->record_count;
	
			$totalAmount = $BestCust[$i]->total;

			$this->ObTpl->set_var("TPL_VAR_BESTCUSTOMER",$BestCust[$i]->vFirstName." ".$BestCust[$i]->vLastName);	
			$this->ObTpl->set_var("TPL_VAR_BESTNUMORDERS",$OrderCount);	
			$this->ObTpl->set_var("TPL_VAR_BESTTOTAL",CONST_CURRENCY.$totalAmount);
			
			$this->ObTpl->parse("bestcustomers_blk","TPL_BESTCUSTOMERS_BLK",true);
		}
		return($this->ObTpl->parse("return","bestCust"));
		
	}
	#FUNCTION TO DISPLAY PRODUCT REPORT
	function m_showProductReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("pReport",$this->productTemplate);
		#INTIALIZING TEMPLATE BLOCKS		 
		$this->ObTpl->set_block("pReport","TPL_MESSAGE_BLK","dspMess_blk");
		$this->ObTpl->set_block("pReport","TPL_PRODUCT_BLK","dspProduct_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_LINK_BLK","link_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_EDIT_LINK_BLK","edit_link_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_NOLINK_BLK","nolink_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_EDIT_NOLINK_BLK","noeditlink_blk");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
		$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
		$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		$this->ObTpl->set_var("PagerBlock1", "");
		$this->ObTpl->set_var("PagerBlock2", "");
		$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");

		if(!isset($this->request['orderby'])){
			$this->request['orderby']="vTitle";
		}
		if(!isset($this->request['order_type'])){
			$this->request['order_type']="";
		}
		if(!isset($this->request['search']))
		{
			$this->request['search']="";
		}
		
		 #QUERY TO GET PRODUCT REPORT 
		$query1 = "SELECT Distinct vTitle,vSku,fPrice,iInventory, F.iState as state,iProdid_PK,vOwnerType	FROM ".PRODUCTS." D LEFT JOIN ".FUSIONS." F ON iProdId_PK = iSubId_FK AND vType='product'";

		$this->request['search']=trim($this->request['search']);
		if(!empty($this->request['search']))
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT",$this->libFunc->m_displayContent($this->request['search']));
			if($this->request['method']=="kit")
			{
				$query1.="";
				$this->ObTpl->set_var("TPL_VAR_MSEL3","selected");
				$this->ObTpl->set_var("TPL_VAR_MSEL2","");
				$this->ObTpl->set_var("TPL_VAR_MSEL1","");
			}
			elseif($this->request['method']=="sku")
			{
				$query1.=" WHERE vSku LIKE '%".$this->libFunc->m_addToDB($this->libFunc->m_addToDB($this->request['search']))."%'";
				$this->ObTpl->set_var("TPL_VAR_MSEL2","selected");
				$this->ObTpl->set_var("TPL_VAR_MSEL1","");
				$this->ObTpl->set_var("TPL_VAR_MSEL3","");
			}
			else
			{
				$query1.=" WHERE vTitle LIKE '%".$this->libFunc->m_addToDB($this->libFunc->m_addToDB($this->request['search']))."%'";
				$this->ObTpl->set_var("TPL_VAR_MSEL1","selected");
				$this->ObTpl->set_var("TPL_VAR_MSEL2","");
				$this->ObTpl->set_var("TPL_VAR_MSEL3","");
			}
		}

		if($this->request['order_type']=="desc")
		{
			$query1.=" ORDER BY ".$this->request['orderby']." DESC";
			$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
			$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		}
		else
		{
			$query1.=" ORDER BY ".$this->request['orderby']." ASC" ;
			$this->ObTpl->set_var("TPL_VAR_CHECK2","checked='checked'");
			$this->ObTpl->set_var("TPL_VAR_CHECK1","");
		}

				
		if($this->request['orderby']=="vSku")
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL2","selected");
		}
		elseif($this->request['orderby']=="iInventory")
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL3","selected");
		}
		elseif($this->request['orderby']=="fPrice")
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL4","selected");
		}
		elseif($this->request['orderby']=="iState")
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL5","selected");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
		}

		#PAGGING
		$pn			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=home.preport&order_type=".$this->request['order_type']."&orderby=".$this->request['orderby']."&search=".urlencode(stripslashes($this->request['search']));
		$pn->formno=1;
		$navArr	= $pn->create($query1, $this->pageSize, $extraStr);

		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);

		$pn2->formno=2;
		$navArr2	= $pn2->create($query1, $this->pageSize, $extraStr,"top");
		$rowProduct=$navArr['qryRes'];
		$totalRecord=$navArr['totalRecs'];
		$selRecord=$navArr['selRecs'];
		$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$totalRecord);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("link_blk","");
		$this->ObTpl->set_var("nolink_blk","");
		$this->ObTpl->set_var("noeditlink_blk","");
		$this->ObTpl->set_var("edit_link_blk","");

		if($selRecord>0)
		{
			for($i=0;$i<$selRecord;$i++)   
			{
				$this->ObTpl->set_var("noeditlink_blk","");
				$this->ObTpl->set_var("link_blk","");
				$this->ObTpl->set_var("nolink_blk","");
				$this->ObTpl->set_var("edit_link_blk","");
				$iOwner_FK=$this->owner_id($rowProduct[$i]->iProdid_PK,$rowProduct[$i]->vOwnerType);

				if($rowProduct[$i]->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","On");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","Off");
				}
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_TITLE",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));	
				
				$this->ObTpl->set_var("TPL_SUBPRODUCT_ID",$rowProduct[$i]->iProdid_PK);
				$this->ObTpl->set_var("TPL_SHOP_URL",SITE_URL."ecom/adminindex.php");
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_SKU",$this->libFunc->m_displayContent($rowProduct[$i]->vSku));
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_PRICE",$rowProduct[$i]->fPrice);
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_STOCK",$rowProduct[$i]->iInventory);
				$this->ObTpl->set_var("TPL_OWNER_ID",$iOwner_FK);
				$this->ObTpl->set_var("TPL_OTYPE",$rowProduct[$i]->vOwnerType);
				if(trim($iOwner_FK)=='')
				{
					$this->ObTpl->parse("nolink_blk","TPL_NOLINK_BLK");
					$this->ObTpl->parse("noeditlink_blk","TPL_EDIT_NOLINK_BLK");
				}
				else
				{
					$this->ObTpl->set_var("TPL_PRODUCT_URL",SITE_URL."ecom/adminindex.php?action=ec_show.home&owner=".$rowProduct[$i]->iProdid_PK."&type=product");
					$this->ObTpl->parse("link_blk","TPL_LINK_BLK");
					$this->ObTpl->parse("edit_link_blk","TPL_EDIT_LINK_BLK");
				}
				$this->ObTpl->set_var("dspMess_blk","");
				$this->ObTpl->parse("dspProduct_blk","TPL_PRODUCT_BLK",true);	
			}
			if($totalRecord>$this->pageSize)
			{
				$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
			}	
		}
		else
		{

			$this->ObTpl->set_var("dspProduct_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_PRODUCT);
			$this->ObTpl->parse("dspMess_blk","TPL_MESSAGE_BLK");	
		}		

		$this->ObTpl->set_var("FORM_URL",SITE_URL."adminindex.php?action=home.preport");		
		$this->ObTpl->set_var("HELP_URL",SITE_URL."adminindex.php?action=home.help");		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		return($this->ObTpl->parse("return","pReport"));
	}

	#FUNCTION TO SHOW CURRENT STOCKS
	function m_showStockReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("sReport",$this->stockTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("sReport","TPL_MESSAGE_BLK","dspMess_blk");
		$this->ObTpl->set_block("sReport","TPL_PRODUCT_BLK","dspProduct_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_LINK_BLK","link_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_NOLINK_BLK","nolink_blk");
		$this->ObTpl->set_var("PagerBlock1", "");
		$this->ObTpl->set_var("PagerBlock2", "");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		 #QUERY TO GET PRODUCT REPORT 

		$query1 = "SELECT vTitle,iInventory,iProdid_PK";
		$query1.=" FROM ".PRODUCTS;

		#PAGGING
		$pn			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=home.sreport";
		$pn->formno=1;
		$navArr	= $pn->create($query1, $this->pageSize, $extraStr,"top");

		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$pn2->formno=2;
		$navArr2	= $pn2->create($query1,$this->pageSize, $extraStr,"top");
	//	$this->obDb->query=$navArr['query'];
		$rowProduct=$navArr['qryRes'];
		$totalRecord=$navArr['totalRecs'];
		$selRecord=$navArr['selRecs'];

		$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$totalRecord);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		
		$this->ObTpl->set_var("link_blk","");
		$this->ObTpl->set_var("nolink_blk","");
		if($selRecord>0)
		{
			for($i=0;$i<$selRecord;$i++)
			{
				$this->ObTpl->set_var("link_blk","");
				$this->ObTpl->set_var("nolink_blk","");
				
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_TITLE",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
				$this->ObTpl->set_var("TPL_SUBPRODUCT_ID",$rowProduct[$i]->iProdid_PK);
				$this->ObTpl->set_var("TPL_SHOP_URL",SITE_URL."ecom/adminindex.php");
				$this->ObTpl->set_var("TPL_VAR_PRODUCT_STOCK",$rowProduct[$i]->iInventory);
				
				$this->ObTpl->set_var("TPL_PRODUCT_URL",SITE_URL."ecom/adminindex.php?action=ec_show.home&owner=".$rowProduct[$i]->iProdid_PK."&type=product");
				$this->ObTpl->parse("link_blk","TPL_LINK_BLK");	
				
				$this->ObTpl->set_var("dspMess_blk","");
				$this->ObTpl->parse("dspProduct_blk","TPL_PRODUCT_BLK",true);	
			}
			if($totalRecord>$this->pageSize)
			{
				$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
			}
		}
		else
		{
			$this->ObTpl->set_var("dspProduct_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_PRODUCT);
			$this->ObTpl->parse("dspMess_blk","TPL_MESSAGE_BLK");	
		}		
		$this->ObTpl->set_var("FORM_URL",SITE_URL."adminindex.php?action=home.sreport");	
		$this->ObTpl->set_var("HELP_URL",SITE_URL."adminindex.php?action=home.help");		
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		return($this->ObTpl->parse("return","sReport"));
	}

	#FUNCTION TO DISPLAY PRODUCT REPORT
	function m_showContentReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("cReport",$this->contentTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("cReport","TPL_MESSAGE_BLK","dspMess_blk");
		$this->ObTpl->set_block("cReport","TPL_CONTENT_BLK","dspContent_blk");
		$this->ObTpl->set_block("TPL_CONTENT_BLK","TPL_LINK_BLK","link_blk");
		$this->ObTpl->set_block("TPL_CONTENT_BLK","TPL_NOLINK_BLK","nolink_blk");
	
		$this->ObTpl->set_var("dspContent_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);

		$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
		$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
		$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
		$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		$this->ObTpl->set_var("PagerBlock1", "");
		$this->ObTpl->set_var("PagerBlock2", "");

		 #QUERY TO GET PRODUCT REPORT 

		$query1 = "SELECT vTitle,F.iState as state,iContentid_PK,iOwner_FK,vOwnerType FROM ".CONTENTS." C LEFT JOIN ".FUSIONS." F ON iContentid_PK=iSubId_FK AND vType='content'";
		if(!isset($this->request['search']))
		{
			$this->request['search']="";
		}
		if(!isset($this->request['order_type']))
		{
			$this->request['order_type']="";
		}
		if(!isset($this->request['orderby']))
		{
			$this->request['orderby']="vTitle";
		}


		$this->request['search']=trim($this->request['search']);
		if(!empty($this->request['search']))
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT",$this->libFunc->m_displayContent($this->request['search']));

			$query1.=" WHERE ( vTitle LIKE '%".$this->libFunc->m_addToDB($this->libFunc->m_addToDB($this->request['search']))."%'  || tShortDescription  LIKE '%".$this->libFunc->m_addToDB($this->libFunc->m_addToDB($this->request['search']))."%' || tContent  LIKE '%".$this->libFunc->m_addToDB($this->libFunc->m_addToDB($this->request['search']))."%')";
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
		}

		if($this->request['order_type']=="desc")
		{
			$query1.=" ORDER BY ".$this->request['orderby']." DESC";
			$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
			$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		}
		else
		{
			$query1.=" ORDER BY ".$this->request['orderby']." ASC" ;
			$this->ObTpl->set_var("TPL_VAR_CHECK2","checked='checked'");
			$this->ObTpl->set_var("TPL_VAR_CHECK1","");
		}

		if($this->request['orderby']=="iState")
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL2","selected");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
		}
		#PAGGING
		$pn			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=home.creport&order_type=".$this->request['order_type']."&orderby=".$this->request['orderby']."&search=".urlencode(stripslashes($this->request['search']));

		$pn->formno=1;
		$navArr	= $pn->create($query1, $this->pageSize, $extraStr);

		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb,"top");
		$pn2->formno=2;
		$navArr2	= $pn2->create($query1, $this->pageSize, $extraStr);

		$rowContent=$navArr['qryRes'];
		$totalRecord=$navArr['totalRecs'];
		$selRecord=$navArr['selRecs'];
		$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$totalRecord);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		
		if($selRecord>0)
		{
			for($i=0;$i<$selRecord;$i++)
			{
				$this->ObTpl->set_var("nolink_blk","");
				$this->ObTpl->set_var("link_blk","");
				$this->ObTpl->set_var("TPL_VAR_CONTENT_TITLE",$this->libFunc->m_displayContent($rowContent[$i]->vTitle));
				if($rowContent[$i]->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","On");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","Off");
				}
				if($rowContent[$i]->iOwner_FK=='')
				{
					$this->ObTpl->set_var("TPL_CONTENT_URL","#");
					$this->ObTpl->parse("nolink_blk","TPL_NOLINK_BLK");
				}
				else
				{
					$this->ObTpl->set_var("TPL_CONTENT_URL",SITE_URL."ecom/adminindex.php?action=ec_show.home&owner=".$rowContent[$i]->iContentid_PK."&type=content");
					$this->ObTpl->parse("link_blk","TPL_LINK_BLK");
				}
				$this->ObTpl->set_var("TPL_SUBPRODUCT_ID",$rowContent[$i]->iContentid_PK);
				$this->ObTpl->set_var("TPL_OWNER_ID",$rowContent[$i]->iOwner_FK);
				$this->ObTpl->set_var("TPL_OTYPE",$rowContent[$i]->vOwnerType);
				$this->ObTpl->set_var("TPL_SHOP_URL",SITE_URL."ecom/adminindex.php");
				
				$this->ObTpl->set_var("dspMess_blk","");
				$this->ObTpl->parse("dspContent_blk","TPL_CONTENT_BLK",true);	
			}
			if($totalRecord>$this->pageSize)
			{
				$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
			}
		}
		else
		{
			$this->ObTpl->set_var("dspContent_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_CONTENT);
			$this->ObTpl->parse("dspMess_blk","TPL_MESSAGE_BLK");	
		}		
		$this->ObTpl->set_var("FORM_URL",SITE_URL."adminindex.php?action=home.creport");		
		$this->ObTpl->set_var("HELP_URL",SITE_URL."adminindex.php?action=home.help");		
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		return($this->ObTpl->parse("return","cReport"));
	}

	function m_deleteProduct()
	{
		$obFile=new FileUpload(); 
		$this->imagePath=SITE_PATH."/images/";
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
			$this->obDb->query = "select vImage1,vImage2,vImage3,vDownloadablefile from ".PRODUCTS." where iProdid_PK=".$this->request['id'];
			$this->imagePath=$this->imagePath."product/";
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);		
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);
				if(!empty($rs[0]->vDownloadablefile) && file_exists($this->imagePath.$rs[0]->vDownloadablefile))
					$obFile->deleteFile($this->imagePath.$rs[0]->vDownloadablefile);	


				$this->obDb->query = "select iOwner_FK,vOwnerType,iSort from ".FUSIONS." where iSubId_FK=".$this->request['id']."  AND vtype='product'";
				$rsOwner = $this->obDb->fetchQuery();

				$this->obDb->query = "SELECT iSubId_FK,vtype,iSort from ".FUSIONS." where iOwner_FK=".$this->request['id']."  AND vOwnerType='product'";
				$rsChild = $this->obDb->fetchQuery();
				$rsChildCount=$this->obDb->record_count;
			
				for($i=0;$i<$rsChildCount;$i++)
				{
					if($rsChild[$i]->vtype=='product')
					{
						$this->obDb->query = "DELETE from ".PRODUCTS." where iProdid_PK='".$rsChild[$i]->iSubId_FK."'";
						$this->obDb->updateQuery();
					}
					else
					{
						#DELETING CONTENT
						$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK='".$rsChild[$i]->iSubId_FK."'";
						$this->obDb->updateQuery();
					}
				}

				#DELETING PRODUCT
				$this->obDb->query = "DELETE FROM ".PRODUCTS." where iProdid_PK=".$this->request['id'];
				$this->obDb->updateQuery();
				

				#DELETING RELATIONAL ENTRY FROM FUSION
				$this->obDb->query = "DELETE from ".FUSIONS." where (iOwner_FK='".$this->request['id']."' AND vOwnerType='product') OR iSubId_FK=".$this->request['id']."  AND vtype='product'";
				$this->obDb->updateQuery();

				#RESORTING
				if($rsOwner[0]->iOwner_FK!='')
				{
					$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 where (iSort>".$rsOwner[0]->iSort." AND iOwner_FK='".$rsOwner[0]->iOwner_FK."' AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='product') ";
					$this->obDb->updateQuery();
				}
			}
		}
		else
		{
			$this->request['owner']=0;
		}		
		if(!isset($this->request['rtype']))
		{
			$this->request['rtype']="product";
		}
		if($this->request['rtype']=="stock")
		{
			$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.sreport");	
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.preport");	
		}
	}

	function m_deleteContent()
	{
		$obFile=new FileUpload(); 
		$this->imagePath=SITE_PATH."/images/";
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
			$this->obDb->query = "select vImage1,vImage2,vImage3 from ".CONTENTS." where iContentid_PK=".$this->request['id'];
			$this->imagePath=$this->imagePath."content/";
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);		
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);


				#GETTING OWNER OF DELETED DEPARTMENT
				$this->obDb->query = "select iOwner_FK,vOwnerType,iSort from ".FUSIONS." where iSubId_FK=".$this->request['id']."  AND vtype='content'";
				$rsOwner = $this->obDb->fetchQuery();

				#DELETING CHILDS
				$this->obDb->query = "SELECT iSubId_FK,vtype,iSort from ".FUSIONS." where iOwner_FK=".$this->request['id']."  AND vOwnerType='content'";
					$rsChild = $this->obDb->fetchQuery();
					$rsChildCount=$this->obDb->record_count;
					for($i=0;$i<$rsChildCount;$i++)
					{
							#DELETING CONTENT
							$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK='".$rsChild[$i]->iSubId_FK."'";
							$this->obDb->updateQuery();
						
					}
				
				#DELETING CONTENT
				$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK=".$this->request['id'];
				$this->obDb->updateQuery();
				

				#DELETING RELATIONAL ENTRY FROM FUSION
				$this->obDb->query = "DELETE from ".FUSIONS." where (iOwner_FK='".$this->request['id']."' AND vOwnerType='content') OR iSubId_FK=".$this->request['id']."  AND vtype='content'";
				$this->obDb->updateQuery();

				#RESORTING
				if($rsOwner[0]->iOwner_FK!='')
				{
					$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 where (iSort>".$rsOwner[0]->iSort." AND iOwner_FK=".$rsOwner[0]->iOwner_FK." AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='content') ";
					$this->obDb->updateQuery();
				}
			}
		}
		else
		{
			$this->request['owner']=0;
		}			
		$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.creport");	
	}#EF
	function owner_id($productid,$ownertype) {
		$this->obDb->query ="select iOwner_FK from ".FUSIONS." where iSubId_FK={$productid} and vtype='product' and vOwnerType='{$ownertype}' "; 
		$rs_record =$this->obDb->fetchQuery();
		if(count($rs_record)>0)
			return $rs_record[0]->iOwner_FK;
		else
			return '';
	}
}#EC
?>