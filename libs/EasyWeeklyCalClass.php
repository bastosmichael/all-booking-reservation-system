<?php
/**
 * EasyWeeklyCalClass V 1.0. A class that generates a weekly schedule easily configurable *
 * @author Ruben Crespo Alvarez [rumailster@gmail.com] http://peachep.wordpress.com
 */

class EasyWeeklyCalClass {

    var $dia;
    var $mes;
    var $ano;
    var $date;


    function EasyWeeklyCalClass ($dia, $mes, $ano) {

        $this->dia = $dia;
        $this->mes = $mes;
        $this->ano = $ano;
        $this->date = $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
    }


    function showCalendar () {
    
        $Output .= $this->buttonsWeek ($this->dia, $this->mes, $this->ano, $this->date["numDiasMes"]);
        $Output .= $this->buttons ($this->dia, $this->mes, $this->ano, $this->date["numDiasMes"]);
        $Output .= "<table border='1' width='95%'>";
        $Output .= $this->WeekTable ($this->date ["diaMes"], $this->date ["diaSemana"], $this->date["numDiasMes"], $this->date["nombreMes"], $this->dia, $this->mes, $this->ano);
        $Output .= "</table>";

        return $Output;
    }
    
    
    function WeeksInMonth ($mes, $leapYear, $firstDay){
        if ($mes == 1 or $mes == 3 or $mes == 5 or $mes == 7 or $mes == 8 or $mes == 10 or $mes == 12) {
    
            if ($firstDay > 5) {
                return 6;
            } else {
                return 5;
            }
        
        } else if ($mes == 4 or $mes == 6 or $mes == 9 or $mes == 11) {
        
            if ($firstDay == 7) {
                return 6;
            } else {
                return 5;
            }
        
        
        } else if ($mes == 2) {
            
            if ($leapYear == "0" and $firstDay == 1) {
                return 4;
            }else{
                return 5;
            }
            
        }
        
    
    }


    function showDate ($hora, $min, $seg, $mes, $dia, $ano){
        $fecha = mktime ($hora, $min, $seg, $mes, $dia, $ano);

        $cal ["diaMes"] = date ("d", $fecha);
        $cal ["nombreMes"] = date ("F", $fecha);
        $cal ["numDiasMes"] = date ("t", $fecha); 
        
        if (date ("w", $fecha) == "0")
        {
            $cal ["diaSemana"] = 7;
        } else {
            $cal ["diaSemana"] = date ("w", $fecha);
        }
        
        $cal ["nombreDiaSem"] = date ("l", $fecha);
        $cal ["leapYear"] = date ("L", $fecha);
       
        
       
        return $cal;
    }
    

    function dayName ($dia) {
    
        if ($dia == 1)
        {
            $Output = "monday";
        } else if ($dia == 2) {
            $Output = "tuesday";
        } else if ($dia == 3) {
            $Output = "wednesday";
        } else if ($dia == 4) {
            $Output = "thursday";
        } else if ($dia == 5) {
            $Output = "friday";
        } else if ($dia == 6) {
            $Output = "saturday";
        } else if ($dia == 7) {
            $Output = "sunday";
        }

        return $Output;
    }
           

    function previousMonth ($dia, $mes, $ano){
        $mes = $mes-1;
        $mes= $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
        return $mes;
    }
    

    function nextMonth ($dia, $mes, $ano){
        $mes = $mes+1;
        $mes= $this->showDate ("10", "00", "00", $mes, 1, $ano);
        return $mes;
    }
        
    
  
    function numberOfWeek ($dia, $mes, $ano) {
        $firstDay = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);
        $numberOfWeek = ceil (($dia + ($firstDay ["diaSemana"]-1)) / 7);
        return $numberOfWeek;
    }
   


    function WeekTable ($diaMes, $diaSemana, $numDiasMes, $nombreMes, $dia, $mes, $ano) {

        
        if ($diaSemana == 0)
        {
            $diaSemana = 7;
        }
            
        $n = 0;
        
        /*NUMBER OF WEEKS AND WEEK NUMBER*/      
        $WeekNumber = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);
        $WeeksInMonth = $this->WeeksInMonth ($mes, $WeekNumber["leapYear"], $WeekNumber["diaSemana"]); 
        $numberOfWeek = $this->numberOfWeek ($dia, $mes, $ano);
        
        $Output .="<tr>
        <td>".$numberOfWeek."&ordf; week of ".$WeeksInMonth."</td>";

        $resta = $diaSemana - 1;
        $diaMes = $diaMes - $resta;

        //Hasta llegar al dia seleccionado
        for ($i=1; $i < $diaSemana; $i++)
        {

            if ($diaMes < 1)
            {
                $previousMonth = $this->previousMonth ($dia, $mes, $ano);
                $diasAnterior = $previousMonth ["numDiasMes"];
                $nameAnterior = $previousMonth ["nombreMes"];

                if ($mes == 1)
                {
                    $mesVar = 12;
                    $anoVar = $ano - 1;
                    
                } else {
                
                    $mesVar = $mes - 1;
                    $anoVar = $ano;
                }

                $cambio = 1;
                $diaMes = $diasAnterior + $diaMes;
                
            } else {
            
                if ($cambio != 1)
                {
                    $nameAnterior = $nombreMes;
                    $mesVar = $mes;
                    $anoVar = $ano;
                }
            }


            if ($diaMes == $dia)
            {
            
            $Output .="<th style='background:#ffeedd; font-weight:bold;'>".$this->dayName ($i).", ".$nameAnterior." de ".$diaMes."</th>";
                
            }else{
            
            $Output .="<th style='font-weight:bold;'>".$this->dayName ($i).", ".$nameAnterior." ".$diaMes."</th>";
            }


            $diaEnlace[$n]["dia"] = $diaMes;
            $diaEnlace[$n]["mes"] = $mesVar;
            $diaEnlace[$n]["ano"] = $anoVar;


            if ($diaMes == $previousMonth["numDiasMes"])
            {
                $diaMes = 1;
                $cambio = 0;
            }else{
                $diaMes ++;
            }

            $n++;

        }



        //Seguimos a partir del dia seleccionado
        for ($diaSemana; $diaSemana <= 7; $diaSemana++)
        {

            if ($diaMes > $numDiasMes)
            {
                $mesS = $this->nextMonth ($dia, $mes, $ano);
                $nameSiguiente = $mesS ["nombreMes"];
                if ($mes == 12)
                {
                    $mesVar = 1;
                    $anoVar = $ano + 1;
                } else {
                    $mesVar = $mes + 1;
                }

                $cambio = 1;
                $diaMes = 1;

            } else {

                if ($cambio != 1)
                {
                    $nameSiguiente = $nombreMes;
                    $mesVar = $mes;
                    $anoVar = $ano;
                }
            }



            if ($diaMes == $dia)
            {
                $Output .="<th style='background:#ffeedd; font-weight:bold;'>".$this->dayName ($diaSemana).", ".$nameSiguiente." ".$diaMes." </th>";
            }else{
                $Output .="<th style='font-weight:bold;'>".$this->dayName ($diaSemana).", ".$nameSiguiente." ".$diaMes." </th>";
            }

            $diaEnlace[$n]["dia"] = $diaMes;
            $diaEnlace[$n]["mes"] = $mesVar;
            $diaEnlace[$n]["ano"] = $anoVar;
            $n++;

            $diaMes ++;
            
        }


        $Output .="</tr>";


        for ($i=0; $i < 24;$i++)
        {
            $Output .="<tr>";

            $Output .="
<td><b>".$i.":00</b></td>";


            for ($n=0; $n<=6; $n++)
            {

                $Output .= "<td><input type='radio' name='programDay' value='".$i."&".$diaEnlace[$n]["dia"]."&".$diaEnlace[$n]["mes"]."&".$diaEnlace[$n]["ano"]."' />Select !!</td>";

            }


            $Output .="</tr>";
        }

        return $Output;
    }



	function buttonsWeek ($dia, $mes, $ano, $numDiasMes) {
		$thisMonth= $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
		$thisMontOne = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);
	    $previousMonth = $this->previousMonth ($dia, $mes, $ano);
        $WeeksInMonth = $this->WeeksInMonth ($mes, $thisMonth["leapYear"], $thisMonth["diaSemana"]);
        $numberOfWeek = $this->numberOfWeek ($dia, $mes, $ano);      
        $diasRestan = (7 - $thisMonth["diaSemana"]);
  

        //BOTON ANT
        if ($dia <= 7)
        {
        
         $ant = $previousMonth["numDiasMes"] - (($thisMontOne["diaSemana"]-1)) + 1;
            $mesAnt = $mes - 1;

            if ($mes == 1)
            {
                $anoAnt = $ano-1;
                $mesAnt = 12;
            } else {
                $anoAnt = $ano;
            }


        }else{
        
            $ant = $dia - ($thisMonth["diaSemana"] + 6);
            $mesAnt= $mes;
            $anoAnt = $ano;
        }




        //BOTON POST
        if ($numberOfWeek == $WeeksInMonth)
        {
            $post="1";
            $mesPost=$mes+1;

            if ($mes == 12)
            {
                $anoPost = $ano+1;
                $mesPost = 1;
            } else {
                $anoPost = $ano;
            }

        }else{

            $post=$dia+($diasRestan+1);
            $mesPost=$mes;
            $anoPost = $ano;
        }


        $Output .= "<p style='font-weight:bold; font-size:0.8em;'>

<a href='".$PHP_SELF."?dia=".$ant."&mes=".$mesAnt."&ano=".$anoAnt."'>&laquo; previous week</a> |

<a href='".$PHP_SELF."?dia=".$post."&mes=".$mesPost."&ano=".$anoPost."'>next week &raquo;</a>
</p>";

        return $Output;
	
	}




    function buttons ($dia, $mes, $ano, $numDiasMes){
        $previousMonth = $this->previousMonth ($dia, $mes, $ano);
        $nextMonth = $this->nextMonth ($dia, $mes, $ano);

        $ant= $dia-1;


        //BOTON ANT
        if ($dia == 1)
        {
            $ant = $previousMonth ["numDiasMes"];
            $mesAnt = $mes - 1;

            if ($mes == 1)
            {
                $anoAnt = $ano-1;
                $mesAnt = 12;
            } else {
                $anoAnt = $ano;
            }


        }else{
            $ant = $dia - 1;
            $mesAnt= $mes;
            $anoAnt = $ano;
        }




        //BOTON POST
        if ($dia == $numDiasMes)
        {
            $post="1";
            $mesPost=$mes+1;

            if ($mes == 12)
            {
                $anoPost = $ano+1;
                $mesPost = 1;
            } else {
                $anoPost = $ano;
            }

        }else{

            $post=$dia+1;
            $mesPost=$mes;
            $anoPost = $ano;
        }


        $Output .= "<p style='font-weight:bold; font-size:0.8em;'>

<a href='".$PHP_SELF."?dia=".$ant."&mes=".$mesAnt."&ano=".$anoAnt."'>&laquo; previous</a> |

<a href='".$PHP_SELF."?dia=".$post."&mes=".$mesPost."&ano=".$anoPost."'>next &raquo;</a>
</p>";

        return $Output;
    }



}//End of WeeklyCalendar Class


?>