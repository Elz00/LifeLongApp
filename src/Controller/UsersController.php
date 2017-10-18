<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[] paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    
    public function beforeFilter(Event $event)
    {
            parent::beforeFilter($event);
            $this->Auth->allow(['logout']);
    } 
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        //$roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        //$roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function login()
    {   
        if ($this->request->is('post')) {
            if($this->request->data('form') == '1') {
                $user = $this->Auth->identify();
                if ($user) {
                        $this->Auth->setUser($user);
                        return $this->redirect($this->Auth->redirectUrl());
                }
                $this->Flash->error(__('Invalid username or password, try again'));
            } else if($this->request->data('form') == '2') {
                $emailEmp = $this->request->data('email');
                $this->sendFormationPlan($emailEmp);
            }
        }
        
    }

    public function logout()
    {
            return $this->redirect($this->Auth->logout());
    }
    
    public function isAuthorized($user) {
        if(isset($user['role']) && $user['role'] === 'Administrator') {
            return true;
        }
        
        return false;
    }
    
    public function sendFormationPlan($emailEmp) {
        $this->loadModel('Employees');
        $employee = $this->Employees->find()->where(['email' => $emailEmp])->first();
        $curr_timestamp = date('Y-m-d H:i:s');
        
        if($employee->last_sent_formation_plan == null || !$employee->last_sent_formation_plan->wasWithinLast('24 hours')) {
            if($employee != null) {
                /*
                $email = new Email('default');
                $email->to($emailEmp)
                    ->subject("Formation plan")
                    ->send("Formation plan");
                */
                    
                $employee->last_sent_formation_plan = $curr_timestamp;
                TableRegistry::get('Employees')->save($employee);
                $this->Flash->success(__('Your formation plan has been sent to your email. Thank you!'));
            } else {
                $this->Flash->success(__('Your formation plan has been sent to your email. Thank you!'));
            }
        } else {
            $this->Flash->error(__('You must wait 24 hours before asking your formation plan again.'));
        }
        
    }
}