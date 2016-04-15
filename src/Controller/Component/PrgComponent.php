<?php

namespace Search\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

class PrgComponent extends Component {

    /**
     * Checks if the current request has posted data and redirects the users
     * to the same action after converting the post data into GET params
     *
     * @return void|Cake\Network\Response
     */
    public function startup() {
        $controller = $this->_registry->getController();
        $request = $controller->request;
        $session = $request->session();

        $filter_session_name = 'Search' . $request->here;

        if (!$request->is('post')) {
            if ($session->check($filter_session_name)) {
                $referer = $request->referer(true);
                
                if (strpos($request->referer(true), '?')) {
                    $referer = explode('?', $request->referer(true))[0];
                }
                
                if (empty($request->query['page']) && $referer == $request->here) {
                    $request->query['page'] = 1;
                }
                
                $request->query = array_merge($session->read($filter_session_name), $request->query);
            }

            $request->data = $request->query;

            if (!empty($request->query['filter'])) {
                if (!empty($request->data['filter']))
                    unset($request->data['filter']);
                
                $session->write($filter_session_name, $request->data);
            }
            return;
        }

        $session->write($filter_session_name, $request->data);
        return $controller->redirect($request->params['pass'] + ['?' => $request->data]);
    }

}
