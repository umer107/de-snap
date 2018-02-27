<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class GridViewTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function saveGridView($data)
	{
     	try{
			if($data['hiddenSelectGridView'] == ''){
				unset($data['hiddenSelectGridView']);
				unset($data['hiddenGenerateNewGridView']);
				$result = $this->tableGateway->insert($data);
				if($result > 0){
					return $this->tableGateway->lastInsertValue.'!(@%&)'.$data['view_title'].'!(@%&)'.$data['columns_list'];
				} else {
					return 0;
				}
			} else {
				if($data['hiddenGenerateNewGridView']){
					unset($data['hiddenSelectGridView']);
					unset($data['hiddenGenerateNewGridView']);
					$result = $this->tableGateway->insert($data);
					if($result > 0){
						return $this->tableGateway->lastInsertValue.'!(@%&)'.$data['view_title'].'!(@%&)'.$data['columns_list'];
					} else {
						return 0;
					}
				} else {
					$data['modified_date'] = date('Y-m-d H:i:s');
					$hiddenSelectGridView = $data['hiddenSelectGridView'];
					unset($data['hiddenSelectGridView']);
					unset($data['hiddenGenerateNewGridView']);
					$result = $this->tableGateway->update($data, array('id' => $hiddenSelectGridView));
					if($result > 0){
						return $hiddenSelectGridView.'!(@%&)'.$data['view_title'].'!(@%&)'.$data['columns_list'];
					} else {
						return 0;
					}
				}
			}
		}catch(\Exception $e){echo $e->getMessage ();exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
	 
	 public function chkViewExist($where){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_gridview')
				   ->columns(array('id'))
				   ->where($where);
				   
			$counter = $this->tableGateway->selectWith($select);
			
			return count($counter);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	 
	public function getGridViews($id, $gridType)
	{
     	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from("de_gridview");
			$select->columns(array("id", "view_title", "is_default"));
			$select->where("customer_id =".$id." AND grid_type ='".$gridType."'");
			$select->order("id DESC");
			$result = $this->tableGateway->selectWith($select);
			$dataResult = $result->toArray();
			return $dataResult;
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
	 
	 public function getGridViewById($id)
	{
     	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from("de_gridview");
			$select->columns(array("*"));
			$select->where("id =".$id);
			$result = $this->tableGateway->selectWith($select);
			$dataResult = $result->current();
			return $dataResult;
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
	 
	 public function deleteGridView($id)
	{
     	try{
			return $this->tableGateway->delete(array('id' => $id));
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
     
     public function setGridViewDefaultId($customer_id, $id, $grid_type) {
		try{
			/* Remove all other defaults (should be just one) */
			$data['is_default'] = 0;
			$this->tableGateway->update($data, array('customer_id' => $customer_id, 'grid_type' => $grid_type, 'is_default' => 1));
     		
			/* Now set just the one passed in */
			$data['is_default'] = 1;
			$this->tableGateway->update($data, array('customer_id' => $customer_id, 'grid_type' => $grid_type, 'id' => $id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     	
     }
}
