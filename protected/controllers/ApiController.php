<?php
class ApiController extends Controller
{

    public function filters()
    {
            return array();
    }
 
    // Actions
    public function actionList()
    {
	
	if(isset($_GET['id'])){
		$id    = intval($_GET['id']);
		$model = Todo::model()->findByPk($id);
		$model->delete();
		exit;
	}
		
		
		$arr = '';
		$todos = Todo::model()->findAll();
		
		$i = 0;
		foreach($todos AS $todo){
			$arr[$i]['id'] = $todo->id_todo;
			$arr[$i]['title'] = $todo->title;
			
			if($todo->status == 1):
				$arr[$i]['completed'] = 'yes';
			else:
				$arr[$i]['completed'] = 'no';
			endif;
		$i++;
		}
		echo json_encode($arr);
    
	}
    public function actionView()
    {
    }
    public function actionCreate()
    {
		$post = file_get_contents("php://input");
		$data = CJSON::decode($post, true);
		$model = new Todo;
		$model->title = $data['title'];
		$data['completed'] == "no" ? $c = 0 : $c=1;
		$model->status = $c;	
		$model->save();
	}
    public function actionUpdate()
    {
		$post = file_get_contents("php://input");
		$data = CJSON::decode($post, true);
		$model = Todo::model()->findByPk($data['id']);
		$data['completed'] == 'yes' ? $c = 1 : $c = 0;
		$model->status = $c;
		$model->save();
    }
    public function actionDelete()
    {
	$post = file_get_contents("php://input");
	$data = CJSON::decode($post, true);
	print_r($data);
    }
}