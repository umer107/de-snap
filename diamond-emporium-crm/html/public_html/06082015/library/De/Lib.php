<?php
namespace De;

final class Lib {

	public static function dbDateFormat($date) {
		list($d ,$m, $y) = explode('/', $date);	
		return strtotime("$y-$m-$d") ? date('Y-m-d', strtotime("$y-$m-$d")) : false;
	}
	
	public static function isTaskOverDue($taskData) {
		if(empty($taskData['due_date']) || !strtotime($taskData['due_date'])){
			$dueDate = null;
		}else{
			$dueDate = $taskData['due_date'];
			if($taskData['due_date_repeat_status'] > 1 && empty($taskData['due_date_end_on'])){
				$dueDate = null;
			}elseif($taskData['due_date_repeat_status'] > 1 && !empty($taskData['due_date_end_on'])){
				$dueDate = $taskData['due_date_end_on'];
			}
			
			if(empty($dueDate))
				$isOverdue = 0;
			else	
				$isOverdue = strtotime($dueDate) >= strtotime(date("Y-m-d")) ? 0 : 1;
			
			return $isOverdue;
		}
	}
}