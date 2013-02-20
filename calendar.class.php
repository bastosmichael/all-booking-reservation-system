<?php
################################################################################
##              -= YOU MAY NOT REMOVE OR CHANGE THIS NOTICE =-                 #
## --------------------------------------------------------------------------- #
##  ApPHP Calendar version 1.0.2 (28.08.2009)                                  #
##  Developed by:  ApPhp <info@apphp.com>                                      #
##  License:       GNU GPL v.2                                                 #
##  Site:          http://www.apphp.com/php-calendar/                          #
##  Copyright:     ApPHP Calendar (c) 2009. All rights reserved.               #
##                                                                             #
################################################################################
// Include WordPress
define('WP_USE_THEMES', true);
require('../../wp-load.php');
//query_posts('showposts=1');
//$postid=isset($_GET['postid'])?$_GET['postid']:"";
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

class Calendar{
	
	
	// PUBLIC
	// --------
	// __construct()
	// __destruct()
	// Show()
	// SetCalendarDimensions
	// SetCaption
	// SetWeekStartedDay
	// SetWeekDayNameLength
	// 
	// STATIC
	// ----------
	// Version
	// 
	// PRIVATE
	// --------
	// SetDefaultParameters
	// GetCurrentParameters
	// DrawJsFunctions
	// DrawYear
	// DrawMonth	
	// DrawMonthSmall
	// DrawWeek
	// DrawDay
	// DrawTypesChanger
	// DrawDateJumper
	// DrawTodayJumper
	// --------
	// isYear
	// isMonth
	// isDay
	// ConvertToDecimal

	//--- PUBLIC DATA MEMBERS --------------------------------------------------
	public $error;
	
	//--- PROTECTED DATA MEMBERS -----------------------------------------------
	protected $weekDayNameLength;
	
	//--- PRIVATE DATA MEMBERS -------------------------------------------------
	private  $arrWeekDays;
	private  $arrMonths;
	private  $arrViewTypes;
	private  $defaultView;
	private  $defaultAction;

	private  $arrParameters;
	private  $arrToday;
	private  $prevYear;
	private  $nextYear;
	private  $prevMonth;
	private  $nextMonth;
	
	private  $isDrawNavigation;
	
	private  $crLt;
	private  $caption;
	private  $calWidth;		
	private  $calHeight;
	private  $cellHeight;

	static private $version = "1.0.2";
	
		
	//--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
	//--------------------------------------------------------------------------
	function __construct()
	{
		$this->defaultView   = "monthly";
		$this->defaultAction = "view";
		
		// possible values 1,2,....7
		$this->weekStartedDay = 1;
		
		$this->weekDayNameLength = "short"; // short|long
		
		$this->arrWeekDays = array();
		$this->arrWeekDays[0] = array("short"=>SUN, "long"=>SUN1);
		$this->arrWeekDays[1] = array("short"=>MON, "long"=>MON1);
		$this->arrWeekDays[2] = array("short"=>TUE, "long"=>TUE1);
		$this->arrWeekDays[3] = array("short"=>WED, "long"=>WED1);
		$this->arrWeekDays[4] = array("short"=>THU, "long"=>THU1);
		$this->arrWeekDays[5] = array("short"=>FRI, "long"=>FRI1);
		$this->arrWeekDays[6] = array("short"=>SAT, "long"=>SAT1);

		$this->arrMonths = array();
		$this->arrMonths["1"] = JANNUARY;
		$this->arrMonths["2"] = FEBRUARY;
		$this->arrMonths["3"] = MARCH;
		$this->arrMonths["4"] = APRIL;
		$this->arrMonths["5"] = MAY;
		$this->arrMonths["6"] = JUNE;
		$this->arrMonths["7"] = JULY;
		$this->arrMonths["8"] = AUGUST;
		$this->arrMonths["9"] = SEPTEMBER;
		$this->arrMonths["10"] = OCTOBER;
		$this->arrMonths["11"] = NOVEMBER;
		$this->arrMonths["12"] = DECEMBER;
		
		$this->arrViewTypes = array();
		$this->arrViewTypes["daily"]   = "Daily";
		$this->arrViewTypes["weekly"]  = "Weekly";
		$this->arrViewTypes["monthly"] = "Monthly";
		$this->arrViewTypes["yearly"]  = "Yearly";
		
		$this->arrParameters = array();
		$this->SetDefaultParameters();

		$this->arrToday  = array();
		$this->prevYear  = array();
		$this->nextYear  = array();
		$this->prevMonth = array();
		$this->nextMonth = array();
		
		$this->isDrawNavigation = true;
		
		$this->crLt = "\n";
		$this->caption = "";
		$this->calWidth = "800px";
		$this->calHeight = "470px";
		$this->celHeight = number_format(((int)$this->calHeight)/6, "0")."px";
	}
	
	//--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
	//--------------------------------------------------------------------------
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

	
	//==========================================================================
    // PUBLIC DATA FUNCTIONS
	//==========================================================================			
	/**
	 *	Show Calendar
	 *
	*/	
	function Show()
	{
		$this->GetCurrentParameters();
		$this->DrawJsFunctions();
		
		echo "<div id='calendar' style='width:".$this->calWidth.";'>".$this->crLt;		
		
		// draw calendar header
		echo "<table id='calendar_header'>".$this->crLt;
		echo "<tr>";
		echo "<th class='caption_left'></th>";
		//echo "<th class='caption'>".writeCart()."</th>";
        echo "<th class='caption'></th>";
		echo "<th class='types_changer'></th>";
		echo "</tr>".$this->crLt;
		echo "</table>";

		
		switch($this->arrParameters["view_type"])
		{			
			case "daily":
				$this->DrawDay();
				break;
			case "weekly":
				$this->DrawWeek();
				break;
			case "yearly":
				$this->DrawYear();
				break;			
			default:
			case "monthly":
				$this->DrawMonth();
				break;
		}
		
		echo "</div>".$this->crLt;
        echo $this->crLt."<!-- This script was generated by ApPHP Calendar v.".self::$version." (http://www.apphp.com) -->".$this->crLt;
		
	}
	
	
	/**
	 *	Set calendar dimensions
	 *  	@param $width
	 *  	@param $height
	*/
	function SetCalendarDimensions($width = "", $height = "")
	{
		$this->calWidth = ($width != "") ? $width : "800px";
		$this->calHeight = ($height != "") ? $height : "470px";
		$this->celHeight = number_format(((int)$this->calHeight)/6, "0")."px";
	}

	/**
	 *	Check if parameters is 4-digit year
	 *  	@param $year - string to be checked if it's 4-digit year
	*/	
	function SetCaption($caption_text = "")
	{
		$this->caption = $caption_text;
	}

    function SetCatid($categoryid = "")
	{
		$this->categoryid = $categoryid;
	}
	
	/**
	 *	Set week started day
	 *  	@param $started_day - started day of week 1...7
	*/	
	function SetWeekStartedDay($started_day = "1")
	{
		if(is_numeric($started_day) && (int)$started_day >= 1 && (int)$started_day <= 7){
			$this->setWeekStartedDay = (int)$started_day;
		}
	}

	/**
	 *	Set week day name length 
	 *  	@param $length_name - "short"|"long"
	*/	
	function SetWeekDayNameLength($length_name = "short")
	{
		if(strtolower($length_name) == "long"){
			$this->weekDayNameLength = "long";
		}
	}
	
	//==========================================================================
    // STATIC
	//==========================================================================		
	/**
	 *	Return current version
	*/	
	static function Version()
	{
		return self::$version;
	}
	
	
	
	//==========================================================================
    // PRIVATE DATA FUNCTIONS
	//==========================================================================		
	/**
	 *	Set default parameters
	 *
	*/	
	function SetDefaultParameters()
	{
		$this->arrParameters["year"]  = date("Y");
		$this->arrParameters["month"] = date("m");
		$this->arrParameters["month_full_name"] = date("F");
		$this->arrParameters["day"]   = date("d");
		$this->arrParameters["view_type"] = $this->defaultView;
		$this->arrParameters["action"] = "display";
		$this->arrToday = getdate();

		// get current file
		//$this->arrParameters["current_file"] = $_SERVER["SCRIPT_NAME"];
		//$parts = explode('/', $this->arrParameters["current_file"]);
        global $wp_query, $post;
        $post = $wp_query->post;
        $post_id = $post->ID;
		$this->arrParameters["current_file"] = get_bloginfo('wpurl').'/index.php?page_id='.$post_id;
	}

	/**
	 *	Get current parameters - read them from URL
	 *
	*/	
	function GetCurrentParameters()
	{
		
		$year 		= (isset($_GET['year']) && $this->isYear($_GET['year'])) ? $this->remove_bad_chars($_GET['year']) : date("Y");
		$month 		= (isset($_GET['month']) && $this->isMonth($_GET['month'])) ? $this->remove_bad_chars($_GET['month']) : date("m");
		$day 		= (isset($_GET['day']) && $this->isDay($_GET['day'])) ? $this->remove_bad_chars($_GET['day']) : date("d");
		$view_type 	= (isset($_GET['view_type']) && array_key_exists($_GET['view_type'], $this->arrViewTypes)) ? $this->remove_bad_chars($_GET['view_type']) : "monthly";
	
		$cur_date = getdate(mktime(0,0,0,$month,$day,$year));
		
		///echo "<br>3--";
		///print_r($cur_date);
		
		$this->arrParameters["year"]  = $cur_date['year'];
		$this->arrParameters["month"] = $this->ConvertToDecimal($cur_date['mon']);
		$this->arrParameters["month_full_name"] = $cur_date['month'];
		$this->arrParameters["day"]   = $day;
		$this->arrParameters["view_type"] = $view_type;
		$this->arrParameters["action"] = "display";
		$this->arrToday = getdate();

		$this->prevYear = getdate(mktime(0,0,0,$this->arrParameters['month'],$this->arrParameters["day"],$this->arrParameters['year']-1));
		$this->nextYear = getdate(mktime(0,0,0,$this->arrParameters['month'],$this->arrParameters["day"],$this->arrParameters['year']+1));

		$this->prevMonth = getdate(mktime(0,0,0,$this->arrParameters['month']-1,$this->arrParameters["day"],$this->arrParameters['year']));
		$this->nextMonth = getdate(mktime(0,0,0,$this->arrParameters['month']+1,$this->arrParameters["day"],$this->arrParameters['year']));
	}

	/**
	 *	Draw javascript functions
	 *
	*/	
	private function DrawJsFunctions()
	{
		echo "<script type='text/javascript'>";
		echo "
			function JumpToDate(){
				var jump_day   = (document.getElementById('jump_day')) ? document.getElementById('jump_day').value : '';
				var jump_month = (document.getElementById('jump_month')) ? document.getElementById('jump_month').value : '';
				var jump_year  = (document.getElementById('jump_year')) ? document.getElementById('jump_year').value : '';
				var view_type  = (document.getElementById('view_type')) ? document.getElementById('view_type').value : '';
				
				__doPostBack('view', view_type, jump_year, jump_month, jump_day);
			}
		
			function __doPostBack(action, view_type, year, month, day)
			{			
				var action    = (action != null) ? action : 'view';
				var view_type = (view_type != null) ? view_type : 'monthly';
				var year      = (year != null) ? year : '".$this->arrToday["year"]."';
				var month     = (month != null) ? month : '".$this->ConvertToDecimal($this->arrToday["mon"])."';
				var day       = (day != null) ? day : '".$this->arrToday["mday"]."';
			
				document.location.href = '".$this->arrParameters["current_file"]."&action='+action+'&view_type='+view_type+'&year='+year+'&month='+month+'&day='+day;
			}

            function openDir( form ) {
	        var newIndex = form.cat.selectedIndex;
	        if ( newIndex == 0 ) {
		    alert( \"Please select a category!\" );
	        } else {
		    cururl = form.cat.options[ newIndex ].value;
		    window.location.assign( cururl );
	        }
            }
		";
		echo "</script>";
		
	}

	/**
	 *	Draw yearly calendar
	 *
	*/	
	private function DrawYear()
	{
		$this->celHeight = "20px";
		echo "<table class='year_container'>".$this->crLt;
		echo "<tr>".$this->crLt;
			echo "<th colspan='3'>";
				echo "<table class='table_navbar'>".$this->crLt;
				echo "<tr>";
				echo "<th class='tr_navbar_left' valign='middle'>
					  ".$this->DrawDateJumper(false, false, false)."
					  </th>".$this->crLt;
				echo "<th class='tr_navbar'></th>".$this->crLt;
				echo "<th class='tr_navbar_right'>				
					  <a href=\"javascript:__doPostBack('view', 'yearly', '".$this->prevYear['year']."', '".$this->arrParameters['month']."', '".$this->arrParameters['day']."')\">".$this->prevYear['year']."</a> |
					  <a href=\"javascript:__doPostBack('view', 'yearly', '".$this->nextYear['year']."', '".$this->arrParameters['month']."', '".$this->arrParameters['day']."')\">".$this->nextYear['year']."</a>
					  </th>".$this->crLt;
				echo "</tr>".$this->crLt;
				echo "</table>".$this->crLt;
			echo "</td>".$this->crLt;
		echo "</tr>".$this->crLt;

		echo "<tr>";
		for($i = 1; $i <= 12; $i++){
			echo "<td align='center' valign='top'>";
			echo "<a href=\"javascript:__doPostBack('view', 'monthly', '".$this->arrParameters['year']."', '".$this->ConvertToDecimal($i)."', '".$this->arrParameters['day']."')\"><b>".$this->arrMonths["$i"]."</b></a>";
			$this->DrawMonthSmall($this->arrParameters['year'], $this->ConvertToDecimal($i));
			echo "</td>";
			if(($i != 1) && ($i % 3 == 0)) echo "</tr><tr>";
		}
		echo "</tr>";
		echo "<tr><td nowrap height='5px'></td></tr>";
		echo "</table>";
	}

	/**
	 *	Draw monthly calendar
	 *
	*/
	private function DrawMonth()
	{

		// today, first day and last day in month
		$firstDay = getdate(mktime(0,0,0,$this->arrParameters['month'],1,$this->arrParameters['year']));
		$lastDay  = getdate(mktime(0,0,0,$this->arrParameters['month']+1,0,$this->arrParameters['year']));

		///print_r($firstDay);
		
		// Create a table with the necessary header informations
		echo "<table class='month'>".$this->crLt;
		echo "<tr>";
			echo "<th colspan='7'>";
				echo "<table class='table_navbar'>".$this->crLt;
				echo "<tr>";
				echo "<th class='tr_navbar_top'>

					  &nbsp;".Jumpcat()."</th>".$this->crLt;
				echo "</tr><tr><th class='tr_navbar'>";
				echo " <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->prevMonth['year']."', '".$this->ConvertToDecimal($this->prevMonth['mon'])."', '".$this->arrParameters['day']."')\">&laquo;&laquo;</a> ";
				echo $this->arrParameters['month_full_name']." - ".$this->arrParameters['year'];
				echo " <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->nextMonth['year']."', '".$this->ConvertToDecimal($this->nextMonth['mon'])."', '".$this->arrParameters['day']."')\">&raquo;&raquo;</a> ";
				echo "</th>".$this->crLt;
				echo "<th class='tr_navbar_right'>				
					  <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->prevYear['year']."', '".$this->arrParameters['month']."', '".$this->arrParameters['day']."')\">".$this->prevYear['year']."</a> |
					  <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->nextYear['year']."', '".$this->arrParameters['month']."', '".$this->arrParameters['day']."')\">".$this->nextYear['year']."</a>
					  </th>".$this->crLt;
				echo "</tr>".$this->crLt;
				echo "</table>".$this->crLt;
			echo "</td>".$this->crLt;
		echo "</tr>".$this->crLt;
		echo "<tr class='tr_days'>";
			for($i = $this->weekStartedDay-1; $i < $this->weekStartedDay+6; $i++){
				echo "<td class='th'>".$this->arrWeekDays[($i % 7)][$this->weekDayNameLength]."</td>";
			}
		echo "</tr>".$this->crLt;
		
		// Display the first calendar row with correct positioning
		if ($firstDay['wday'] == 0) $firstDay['wday'] = 7;
		$max_empty_days = $firstDay['wday']-($this->weekStartedDay-1);
		if($max_empty_days < 7){
			echo "<tr class='tr' style='height:".$this->celHeight.";'>".$this->crLt;			
			for($i = 1; $i <= $max_empty_days; $i++){
				echo "<td class='td_empty'>&nbsp;</td>".$this->crLt;
			}
			$actday = 0;
			for($i = $max_empty_days+1; $i <= 7; $i++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $this->arrParameters["month"])) {
					$class = " class='td_actday'";
				} else if ($actday == $this->arrParameters['day']){
					$class = " class='td_selday'";
				} else {
					$class = " class='td'";
				}
                if ($_REQUEST["month"]) {
                $months = $_REQUEST["month"];
                } else {
                $months = date('m');
                }
                if ($_REQUEST["year"]) {
                $years = $_REQUEST["year"];
                } else {
                $years = date('Y');
                }
                if ($_REQUEST["day"]) {
                $dayq = $actday;
                } else {
                $dayq = $actday;
                }
                if ($_REQUEST["month"]) {
                $monthsq = $_REQUEST["month"];
                } else {
                $monthsq = date('m');
                }
                if ($_REQUEST["year"]) {
                $yearsq = $_REQUEST["year"];
                } else {
                $yearsq = date('Y');
                }
                $dates = $yearsq."-".$monthsq."-".$dayq;

                if ($_REQUEST["cat"]) {
                $category = $_REQUEST["cat"];
                } else if ($this->categoryid) {
                $category = $this->categoryid;
                } else {
                $settings = allbook_get_settings();
                $category = $settings['allbook_catinit'];
                }

    //require_once("libs/SQLManager.class.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $querydate = mysql_query("SELECT description, SUM(max) as totale_numero FROM wp_resservation_disp WHERE date = '".$dates."' AND category= ".$category."");
    //$array_val = $db->getArray($querydate, true);
    //echo $querydate;
    if(@mysql_num_rows($querydate) != 0)
	{
    $row = mysql_fetch_array( $querydate );
    if ($row['totale_numero'] >= 1) {
    $posti = $row['totale_numero']." ".PLACES."";
    }  else {
    $posti = "0 ".PLACES."";
    }  }
    $cdate = $dates." 23:59";
    $prima_data = strtotime($cdate);
    $oggi = strtotime(date('Y-m-d H:i'));

if ($prima_data < $oggi) {

                echo "<td$class><div class='days'>$actday</div></td>".$this->crLt;
                 } else {
				echo "<td$class><div class='days'>$actday </div><div class='places'><a href=\"".$this->arrParameters["current_file"]."&action=view&view_type=daily&year=$years&month=$months&day=$actday&cat=$category\">".$posti."</a><div class='description'>".$row['description']."</div></div></td>".$this->crLt;
			} }
			echo "</tr>".$this->crLt;
		}
		//$db->Close();
		//Get how many complete weeks are in the actual month
		$fullWeeks = floor(($lastDay['mday']-$actday)/7);
		
		for ($i=0;$i<$fullWeeks;$i++){
			echo "<tr class='tr' style='height:".$this->celHeight.";'>".$this->crLt;
			for ($j=0;$j<7;$j++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $this->arrParameters["month"])) {
					$class = " class='td_actday'";
				} else if ($actday == $this->arrParameters['day']){				
					$class = " class='td_selday'";				
				} else {
					$class = " class='td'";
				}
                if ($_REQUEST["month"]) {
                $months = $_REQUEST["month"];
                } else {
                $months = date('m');
                }
                if ($_REQUEST["year"]) {
                $years = $_REQUEST["year"];
                } else {
                $years = date('Y');
                }
                if ($_REQUEST["day"]) {
                $dayq = $actday;
                } else {
                $dayq = $actday;
                }
                if ($_REQUEST["month"]) {
                $monthsq = $_REQUEST["month"];
                } else {
                $monthsq = date('m');
                }
                if ($_REQUEST["year"]) {
                $yearsq = $_REQUEST["year"];
                } else {
                $yearsq = date('Y');
                }
                $dates = $yearsq."-".$monthsq."-".$dayq;

                if ($_REQUEST["cat"]) {
                $category = $_REQUEST["cat"];
                } else if ($this->categoryid) {
                $category = $this->categoryid;
                } else {
                $settings = allbook_get_settings();
                $category = $settings['allbook_catinit'];
                }

    //require_once("libs/SQLManager.class.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $querydate = mysql_query("SELECT description, SUM(max) as totale_numero FROM wp_resservation_disp WHERE date = '".$dates."' AND category= ".$category."");
    //$array_val = $db->getArray($querydate, true);
    //echo $querydate;
    if(@mysql_num_rows($querydate) != 0)
	{
    $row = mysql_fetch_array( $querydate );
    if ($row['totale_numero'] >= 1) {
    $posti = $row['totale_numero']." ".PLACES."";
    }  else {
    $posti = "0 ".PLACES."";
    }
    }
    $cdate = $dates." 23:59";
    $prima_data = strtotime($cdate);
    $oggi = strtotime(date('Y-m-d H:i'));

if ($prima_data < $oggi) {
				echo "<td$class><div class='days'>$actday</div></td>".$this->crLt;
                } else {
                echo "<td$class><div class='days'>$actday </div><div class='places'><a href=\"".$this->arrParameters["current_file"]."&action=view&view_type=daily&year=$years&month=$months&day=$actday&cat=$category\">".$posti."</a></div><div class='description'>".$row['description']."</div></td>".$this->crLt;
			}}
			echo "</tr>".$this->crLt;
		}
		//$db->Close();
		//Now display the rest of the month
		if ($actday < $lastDay['mday']){
			echo "<tr class='tr' style='height:".$this->celHeight.";'>".$this->crLt;

			for ($i=0; $i<7;$i++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $this->arrParameters["month"])) {
					$class = " class='td_actday'";
				} else {
					$class = " class='td'";
				}
				if ($actday <= $lastDay['mday']){
				  if ($_REQUEST["month"]) {
                $months = $_REQUEST["month"];
                } else {
                $months = date('m');
                }
                if ($_REQUEST["year"]) {
                $years = $_REQUEST["year"];
                } else {
                $years = date('Y');
                }
                if ($_REQUEST["day"]) {
                $dayq = $actday;
                } else {
                $dayq = $actday;
                 }
                if ($_REQUEST["month"]) {
                $monthsq = $_REQUEST["month"];
                } else {
                $monthsq = date('m');
                }
                if ($_REQUEST["year"]) {
                $yearsq = $_REQUEST["year"];
                } else {
                $yearsq = date('Y');
                }
                $dates = $yearsq."-".$monthsq."-".$dayq;

                if ($_REQUEST["cat"]) {
                $category = $_REQUEST["cat"];
                } else if ($this->categoryid) {
                $category = $this->categoryid;
                } else {
                $settings = allbook_get_settings();
                $category = $settings['allbook_catinit'];
                }

    //require_once("libs/SQLManager.class.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $querydate = mysql_query("SELECT description, SUM(max) as totale_numero FROM wp_resservation_disp WHERE date = '".$dates."' AND category= ".$category."");
    //$array_val = $db->getArray($querydate, true);
    //echo $querydate;
    if(@mysql_num_rows($querydate) != 0)
	{
    $row = mysql_fetch_array( $querydate );
    if ($row['totale_numero'] >= 1) {
    $posti = $row['totale_numero']." ".PLACES."";
    }  else {
    $posti = "0 ".PLACES."";
    }
    }
    $cdate = $dates." 23:59";
    $prima_data = strtotime($cdate);
    $oggi = strtotime(date('Y-m-d H:i'));

if ($prima_data < $oggi) {
                    echo "<td$class><div class='days'>$actday</div></td>".$this->crLt;
                     } else {
					echo "<td$class><div class='days'>$actday</div> <div class='places'><a href=\"".$this->arrParameters["current_file"]."&action=view&view_type=daily&year=$years&month=$months&day=$actday&cat=$category\">".$posti."</a></div><div class='description'>".$row['description']."</div></td>".$this->crLt;
                    }
				} else {
					echo "<td class='td_empty'>&nbsp;</td>".$this->crLt;
				}
			}					
			echo "</tr>".$this->crLt;
		}		
		echo "</table>".$this->crLt;
        //$db->Close();
	}


	/**
	 *	Draw small monthly calendar
	 *
	*/	
	private function DrawMonthSmall($year = "", $month = "")
	{
		if($month == "") $month = $this->arrParameters['month'];
		if($year == "") $year = $this->arrParameters['year'];
		$week_rows = 0;
		
		// today, first day and last day in month
		$firstDay = getdate(mktime(0,0,0,$month,1,$year));
		$lastDay  = getdate(mktime(0,0,0,$month+1,0,$year));
		
		///print_r($firstDay);
		
		// create a table with the necessary header informations
		echo "<table class='month_small'>".$this->crLt;
		echo "<tr class='tr_small_days'>";
			for($i = $this->weekStartedDay-1; $i < $this->weekStartedDay+6; $i++){
				echo "<td class='th_small'>".$this->arrWeekDays[($i % 7)]["short"]."</td>";		
			}
		echo "</tr>".$this->crLt;
		
		// display the first calendar row with correct positioning
		if ($firstDay['wday'] == 0) $firstDay['wday'] = 7;
		$max_empty_days = $firstDay['wday']-($this->weekStartedDay-1);		
		if($max_empty_days < 7){
			echo "<tr class='tr_small' style='height:".$this->celHeight.";'>".$this->crLt;			
			for($i = 1; $i <= $max_empty_days; $i++){
				echo "<td class='td_small_empty'>&nbsp;</td>".$this->crLt;
			}			
			$actday = 0;
			for($i = $max_empty_days+1; $i <= 7; $i++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $this->arrParameters["month"])) {
					$class = " class='td_small_actday'";			
				} else if ($actday == $this->arrParameters['day']){				
					$class = " class='td_small_selday'";
				} else {
					$class = " class='td_small'";
				} 
				echo "<td$class>$actday</td>".$this->crLt;
			}
			echo "</tr>".$this->crLt;
			$week_rows++;
		}
		
		// get how many complete weeks are in the actual month
		$fullWeeks = floor(($lastDay['mday']-$actday)/7);
		
		for ($i=0;$i<$fullWeeks;$i++){
			echo "<tr class='tr_small' style='height:".$this->celHeight.";'>".$this->crLt;
			for ($j=0;$j<7;$j++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $month) && ($this->arrToday['year'] == $year)) {
					$class = " class='td_small_actday'";
				} else if ($actday == $this->arrParameters['day'] && ($this->arrToday['mon'] == $month)){				
					$class = " class='td_small_selday'";				
				} else {
					$class = " class='td_small'";
				}
				echo "<td$class>$actday</td>".$this->crLt;
			}
			echo "</tr>".$this->crLt;
			$week_rows++;			
		}
		
		// now display the rest of the month
		if ($actday < $lastDay['mday']){
			echo "<tr class='tr_small' style='height:".$this->celHeight.";'>".$this->crLt;			
			for ($i=0; $i<7;$i++){
				$actday++;
				if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $month) && ($this->arrToday['year'] == $year)) {
					$class = " class='td_small_actday'";
				} else {
					$class = " class='td_small'";
				}				
				if ($actday <= $lastDay['mday']){
					echo "<td$class>$actday</td>".$this->crLt;
				} else {
					echo "<td class='td_small_empty'>&nbsp;</td>".$this->crLt;
				}
			}					
			echo "</tr>".$this->crLt;
			$week_rows++;
		}
		
		// complete last line
		if($week_rows < 5){
			echo "<tr class='tr_small' style='height:".$this->celHeight.";'>".$this->crLt;			
			for ($i=0; $i<7;$i++){
				echo "<td class='td_small_empty'>&nbsp;</td>".$this->crLt;
			}					
			echo "</tr>".$this->crLt;
			$week_rows++;			
		}
		
		echo "</table>".$this->crLt;
		
	}
	

	/**
	 *	Draw weekly calendar
	 *
	*/
	private function DrawWeek()
	{
		//echo "<br /><font color='#a60000'>This type of calendar view is not available in free version</font>";
	    //return false;

		// today, first day and last day in month
		$firstDay = getdate(mktime(0,0,0,$this->arrParameters['month'],1,$this->arrParameters['year']));
		$lastDay  = getdate(mktime(0,0,0,$this->arrParameters['month']+1,0,$this->arrParameters['year']));
        //require_once("libs/SQLManager.class.php");
        //$db = new SQLManager(true, "", "log.txt", true);
        //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
        $settings = allbook_get_settings();
        //$times = create_time_range($settings['allbook_datastart'], $settings['allbook_dataend'], $settings['allbook_datarange']);

        if ($_REQUEST["cat"]) {
        $category = $_REQUEST["cat"];
        } else {
        $settings = allbook_get_settings();
        $category = $settings['allbook_catinit'];
          }
        $categoryquery = mysql_query("SELECT * FROM wp_resservation_cat WHERE id= '".$category."'");
        $rowcategory = mysql_fetch_array( $categoryquery );

        $rgtime = $rowcategory['rangetime']." mins";
        $rgtimedif = "+".$rowcategory['rangetime']." minutes";

        $times = create_time_range($rowcategory['time_start_cat'], $rowcategory['time_end_cat'], $rgtime);

		// Create a table with the necessary header informations
		echo "<table class='month'>".$this->crLt;
		echo "<tr>";
		echo "<th class='tr_navbar_top' colspan='7'>
			  ".$this->DrawDateJumper(false)."
			  </th>".$this->crLt;
		echo "</tr><tr><th class='tr_navbar' colspan='3'>".$this->arrParameters['month_full_name']." - ".$this->arrParameters['year']."</th>".$this->crLt;
		echo "<th class='tr_navbar_right' colspan='2'>
			  <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->prevYear['year']."')\">".$this->prevYear['year']."</a> |
			  <a href=\"javascript:__doPostBack('view', 'monthly', '".$this->nextYear['year']."')\">".$this->nextYear['year']."</a>
			  </th>".$this->crLt;
		echo "</tr>".$this->crLt;
		echo "<tr class='tr_days'>";
            $m1= date("m");
            $de1= date("d");
            $y1= date("Y");
			 for($l=0; $l<=6; $l++){
				echo "<td class='th'>".date('l',mktime(0,0,0,$m1,($de1+$l),$y1))."</td>";
			}
		echo "</tr>".$this->crLt;

		
		
		// Display the first calendar row with correct positioning
		echo "<tr>".$this->crLt;
		/*if ($firstDay['wday'] == 0) $firstDay['wday'] = 7;
		for($i = 1; $i <= $firstDay['wday']-($this->weekStartedDay-1); $i++){
			echo "<td class='td'>&nbsp;</td>".$this->crLt;
		}*/
		/*$actday = 0;
		for($i = ($firstDay['wday']-($this->weekStartedDay-1))+1; $i <= 7; $i++){
			$actday++;*/
            $m= date("m");
            $de= date("d");
            $y= date("Y");
            if ($_REQUEST["month"]) {
                $months = $_REQUEST["month"];
                } else {
                $months = date('m');
                }
                if ($_REQUEST["year"]) {
                $years = $_REQUEST["year"];
                } else {
                $years = date('Y');
                }
                if ($_REQUEST["day"]) {
                $dayq = $actday;
                } else {
                $dayq = date('d',mktime(0,0,0,$m,($de+1),$y));
                 }
                if ($_REQUEST["month"]) {
                $monthsq = $_REQUEST["month"];
                } else {
                $monthsq = date('m');
                }
                if ($_REQUEST["year"]) {
                $yearsq = $_REQUEST["year"];
                } else {
                $yearsq = date('Y');
                }

            for($i=0; $i<=6; $i++){
			if (($actday == $this->arrToday['mday']) && ($this->arrToday['mon'] == $this->arrParameters["month"])) {
				$class = " class='td_actday'";
			} else {
				$class = " class='td'";
			}
			echo "<td$class>".date('d',mktime(0,0,0,$m,($de+$i),$y))."</td>".$this->crLt;
		}
		echo "</tr>".$this->crLt;
        foreach ($times as $key => $time) {
        echo "<tr>";
        for($a=0; $a<=6; $a++){
          $actday = date('d',mktime(0,0,0,$m,($de+$a),$y));
          $dates = $yearsq."-".$monthsq."-".$actday;
          $qrytime = $times[$key] = date('H:i:s', $time);
          $querydate = mysql_query("SELECT * FROM wp_resservation_disp WHERE date = '".$dates."' AND time_start = '".$qrytime."' AND category= ".$category." LIMIT 1");
          if(@mysql_num_rows($querydate) != 0)
	    {
        $row = mysql_fetch_array( $querydate );
        if ($row['max'] == 1) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">Place available</a>";
} else if($row['max'] >= 2) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">Places availables</a>";
} else {
$avv = "Non Disponibile";
}
        }

        echo "<td$class>".$times[$key] = date('H:i:s', $time);
        echo "<div class='places'>".$row['max']." ".$avv."</div><div class='description'>".$row['description']."</div></td>";
        }
        echo "</tr>";
        }
		echo "</table>".$this->crLt;
		
	}



	/**
	 *	Draw daily calendar
	 *
	*/
	private function DrawDay()
	{
	    $settings = allbook_get_settings();

        if ($_REQUEST["cat"]) {
        $category = $_REQUEST["cat"];
        } else if ($this->categoryid) {
        $category = $this->categoryid;
        } else {
        $settings = allbook_get_settings();
        $category = $settings['allbook_catinit'];
          }
		//$times = create_time_range($settings['allbook_datastart'], $settings['allbook_dataend'], $settings['allbook_datarange']);
       $categoryquery = mysql_query("SELECT * FROM wp_resservation_cat WHERE id= '".$category."'");
       $rowcategory = mysql_fetch_array( $categoryquery );

       $rgtime = $rowcategory['rangetime']." mins";
       $rgtimedif = "+".$rowcategory['rangetime']." minutes";

       $times = create_time_range($rowcategory['time_start_cat'], $rowcategory['time_end_cat'], $rgtime);
// more examples
// $times = create_time_range('9:30am', '5:30pm', '30 mins');
// $times = create_time_range('9:30am', '5:30pm', '1 mins');
// $times = create_time_range('9:30am', '5:30pm', '30 secs');
// and so on
print "<table class='month'>";

//request date
$day = $_REQUEST["day"];
$months = $_REQUEST["month"];
$years = $_REQUEST["year"];
$dates = $years."-".$months."-".$day;
//require_once("libs/SQLManager.class.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
echo "<tr><td>".TIMEINTERVALS."</td><td>".AVAIABILE."</td></tr>";
// format the unix timestamps
foreach ($times as $key => $time) {

$qrytime = $times[$key] = date('H:i:s', $time);
$querydate = mysql_query("SELECT * FROM wp_resservation_disp WHERE date = '".$dates."' AND time_start = '".$qrytime."' AND category= ".$category." LIMIT 1");
//$array_val = $db->getArray($querydate, true);
//echo $querydate;
$row = mysql_fetch_array( $querydate );
if ($row['max'] == 1) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">".PLACEAVV."</a>";
} else if($row['max'] >= 2) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">".PLACEAVVS."</a>";
} else {
$avv = NOTVVS;
}
    print "<tr>";
    echo "<td>".$years."/".$months."/".$day." - ".$times[$key] = date('H:i:s', $time)."</td>";
    echo "<td>".$row['max']." ".$avv."</td>";
    print "</tr>";
}
print "</table>";
//$db->Close();
	}

	/**
	 *	Draw calendar types changer
	 *  	@param $draw - draw or return
	*/
	private function DrawTypesChanger($draw = true)
	{
		$result = "<select class='form_select' name='view_type' id='view_type' onchange=\"document.location.href='".$this->arrParameters["current_file"]."?action=view&view_type='+this.value\">";
		foreach($this->arrViewTypes as $key => $val){
			$result .= "<option value='".$key."' ".(($this->arrParameters['view_type'] == $key) ? "selected='selected'" : "").">".$val."</option>";
		}
		$result .= "</select>";
		
		if($draw){
			echo $result;
		}else{
			return $result;
		}
	}

	/**
	 *	Draw today jumper
	 *  	@param $draw - draw or return
	*/	
	private function DrawTodayJumper($draw = true)
	{
		$result = "<input class='form_button' type='button' value='Today' onclick=\"javascript:__doPostBack('".$this->defaultAction."', '".$this->defaultView."', '".$this->arrToday["year"]."', '".$this->ConvertToDecimal($this->arrToday["mon"])."', '".$this->arrToday["mday"]."')\" />";
	
		if($draw){
			echo $result;
		}else{
			return $result;
		}
	}
	
	/**
	 *	Draw date jumper
	 *  	@param $draw - draw or return
	*/	
	private function DrawDateJumper($draw = true, $draw_day = true, $draw_month = true, $draw_year = true)
	{
		$result = "<form name='frmCalendarJumper' class='class_form'>";

		// draw days ddl
		if($draw_day){
			$result = "<select class='form_select' name='jump_day' id='jump_day'>";
			for($i=1; $i <= 31; $i++){
				$i_converted = $this->ConvertToDecimal($i);
				$result .= "<option value='".$this->ConvertToDecimal($i)."' ".(($this->arrParameters["day"] == $i_converted) ? "selected='selected'" : "").">".$i_converted."</option>";
			}
			$result .= "</select> ";			
		}else{
			$result .= "<input type='hidden' name='jump_day' id='jump_day' value='".$this->arrToday["mday"]."' />";			
		}

		// draw months ddl
		if($draw_month){			
			$result .= "<select class='form_select' name='jump_month' id='jump_month'>";
			for($i=1; $i <= 12; $i++){
				$i_converted = $this->ConvertToDecimal($i);
				$result .= "<option value='".$this->ConvertToDecimal($i)."' ".(($this->arrParameters["month"] == $i_converted) ? "selected='selected'" : "").">".$this->arrMonths[$i]."</option>";
			}
			$result .= "</select> ";			
		}else{
			$result .= "<input type='hidden' name='jump_month' id='jump_month' value='".$this->ConvertToDecimal($this->arrToday["mon"])."' />";			
		}

		// draw years ddl
		if($draw_year){			
			$result .= "<select class='form_select' name='jump_year' id='jump_year'>";
			for($i=$this->arrParameters["year"]-10; $i <= $this->arrParameters["year"]+10; $i++){
				$result .= "<option value='".$i."' ".(($this->arrParameters["year"] == $i) ? "selected='selected'" : "").">".$i."</option>";
			}
			$result .= "</select> ";
		}else{
			$result .= "<input type='hidden' name='jump_year' id='jump_year' value='".$this->arrToday["year"]."' />";			
		}
		
		$result .= "<input class='form_button' type='button' value='Go' onclick='JumpToDate()' />";
		$result .= "</form>";
		
		if($draw){
			echo $result;
		}else{
			return $result;
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Auxilary
	////////////////////////////////////////////////////////////////////////////
	/**
	 *	Check if parameters is 4-digit year
	 *  	@param $year - string to be checked if it's 4-digit year
	*/	
	private function isYear($year = "")
	{
		if(!strlen($year) == 4 || !is_numeric($year)) return false;
		for($i = 0; $i < 4; $i++){
			if(!(isset($year[$i]) && $year[$i] >= 0 && $year[$i] <= 9)){
				return false;	
			}
		}
		return true;
	}

	/**
	 *	Check if parameters is month
	 *  	@param $month - string to be checked if it's 2-digit month
	*/	
	private function isMonth($month = "")
	{
		if(!strlen($month) == 2 || !is_numeric($month)) return false;
		for($i = 0; $i < 2; $i++){
			if(!(isset($month[$i]) && $month[$i] >= 0 && $month[$i] <= 9)){
				return false;	
			}
		}
		return true;
	}

	/**
	 *	Check if parameters is day
	 *  	@param $day - string to be checked if it's 2-digit day
	*/	
	private function isDay($day = "")
	{
		if(!strlen($day) == 2 || !is_numeric($day)) return false;
		for($i = 0; $i < 2; $i++){
			if(!(isset($day[$i]) && $day[$i] >= 0 && $day[$i] <= 9)){
				return false;	
			}
		}
		return true;
	}

	/**
	 *	Convert to decimal number with leading zero
	 *  	@param $number
	*/	
	private function ConvertToDecimal($number)
	{
		return (($number < 10) ? "0" : "").$number;
	}

   	/**
	 *	Remove bad chars from input
	 *	  	@param $str_words - input
	 **/
	private function remove_bad_chars($str_words)
	{
		$found = false;
		$bad_string = array("select", "drop", ";", "--", "insert","delete", "xp_", "%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://", "onmouseover=", "onmouseout=");
		for ($i = 0; $i < count($bad_string); $i++){
			$str_words = str_replace($bad_string[$i], "", $str_words);
		}
		return $str_words;            
	}

}

function create_time_range($start, $end, $by='30 mins') {

    $start_time = strtotime($start);
    $end_time   = strtotime($end);

    $current    = time();
    $add_time   = strtotime('+'.$by, $current);
    $diff       = $add_time-$current;

    $times = array();
    while ($start_time < $end_time) {
        $times[] = $start_time;
        $start_time += $diff;
    }
    $times[] = $start_time;
    return $times;
}

function writeCart() {
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
$cart = $_SESSION['cart'];
if (!$cart) {
return '<p>You have no items in the cart</p>';
} else {
// analyze the cart session variable
$items = explode(',',$cart);
$s = (count($items) > 1) ? 's':'';
return '<p>You have <a href="'.$url.'/index.php?page_id='.$post_id.'&pager=cart">'.count($items).' item'.$s.' in the shopping cart</a></p>';
}
}

function Jumpcat() {

$jumpcat = "<form name='form'>";
$jumpcat .= '<select name="cat" size="1" class="category" onChange="openDir( this.form )">';
$jumpcat .= '<option>'.JUMCAT.' </option>';

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$category = mysql_query("SELECT * FROM wp_resservation_cat");
while($row1 = mysql_fetch_array($category))
        {
            /*** create the options ***/
            $jumpcat .= '<option value="'.$url=get_bloginfo('wpurl').'/index.php?page_id='.$post_id.'&cat='.$row1['id'].'"';
            $jumpcat .= '>'. $row1['name'] . '</option>'."\n";
        }


$jumpcat .= '</select>';
$jumpcat .= '</form>';

return $jumpcat;
}
?>