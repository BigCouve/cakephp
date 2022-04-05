<?php

App::uses('AppController', 'Controller');

class PostsController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Posts';

    public function index(){
        
    }
    public function list() {
        $this->set('list', $this->Post->find('all'));
    }

    public function view($id = null) {
        $this->set('post', $this->Post->findById($id));
    }
    public function add() {
        if ($this->request->is('post')) {
            $this->request->data['Post']['user_id'] = $this->Auth->user('id'); // Adicionada essa linha
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success('Your post has been saved.');
                $this->redirect(array('action' => 'list'));
            }
        }
    }
    
    public function edit($id = null) {
        
        // Campo do id recebe id do post atual
        $this->Post->id = $id;
        if ($this->request->is('get')) {
             $this->request->data = $this->Post->findById($id);
        } else {
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success('Seu Post foi atualizado.');
                $this->redirect(array('action' => 'list'));
            }
        }
        debug($this->Post->find('first'));

    }

    public function delete($id) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        if ($this->Post->delete($id)) {
            $this->Flash->success('O post com o id: ' . $id . ' foi excluído.');
            $this->redirect(array('action' => 'list'));
        }
    }

    public function listProfile(){
        $userLogged = (int) $this->Auth->user('username');
        debug($userLogged); 
        debug($this->Post->query("SELECT * FROM posts WHERE user_id = " . $userLogged));
        // debug(gettype($userLogged));
        $this->set('listOwned', $this->Post->query("SELECT * FROM posts WHERE user_id = 49" ));
    }
    public function isAuthorized($user) {
        // Todos os usuários registrados podem criar posts
        if ($this->action === 'add') {
            return true;
        }
        if (parent::isAuthorized($user)){
            // O dono de um post pode editá-lo e deletá-lo
            if (in_array($this->request->action, array('edit', 'delete'))) {
                $postId = (int) $this->request->params['pass'][0];
                return $this->Post->isOwnedBy($postId, $user['id']);
            }
        }
    }
}
?>