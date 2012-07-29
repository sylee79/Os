<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once "annotations.php";
require_once BASEPATH . '../application/controllers/basewebappcontroller.php';

class baseadmincontroller extends basewebappcontroller
{
    private $loggedInUser;
    protected $currency = 'S$';
    protected $adminBase = '/admin';
    private $hideMenuPages = array('cron', 'login', 'access_denied');
    private $publicAccess = array("cron", "login", "access_denied");
    private $mainMenuPages = array();

    function __construct($hideMenuPages=array(), $mainMenuPages=array())
    {
        parent::__construct();
        $this->load->library('parser');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('LibAdmin');

        _d("URI_STRING : " . uri_string() . ", current_url : " . current_url());

        Doctrine_Manager::connection()->setCharset("utf8");

        $this->hideMenuPages=array_merge($this->hideMenuPages, $hideMenuPages);
        $this->mainMenuPages=array_merge($this->mainMenuPages, $mainMenuPages);

        if (!$this->myUser() && !$this->isPublicAccess()) {
            $this->session->set_userdata("sc_oam_original_uri", current_url());
            redirect("/admin/login");
            return;
        }

        if ($this->myUser()) {
            define("LOGGED_IN_OAM_USER_ID", $this->myUser()->id);
        }

        if (!$this->checkRoleAccess() && !$this->isPublicAccess()) {
            redirect("/manage/access_denied");
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect("/admin");
    }


    protected function isPublicAccess()
    {
        _d("Public access check for current url " . current_url() . " and uri string " . uri_string());
        foreach ($this->publicAccess as $p) {
            if (stripos(current_url(), $p) !== FALSE) {
                _d("Public access returning true for $p");
                return true;
            }
        }

        _d("Public access denied");
        return false;
    }

    private function checkRoleAccess()
    {
        $reflectionMethod = new ReflectionAnnotatedMethod(get_class($this), $this->router->method);
        $requiredRole = $reflectionMethod->getAnnotation('RequiresRole');
        if ($requiredRole !== false) {
            $roleName = $requiredRole->value;
            if ($this->myUser()) {
                $roles = $this->myUser()->role_list;
                if (!is_null($roles)) {
                    $myRoleList = explode(",", $roles);
                    if (in_array($roleName, $myRoleList)) {
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }

    private function getLoggedinUser()
    {
        //TODO: disabling login for now
        //return Doctrine::getTable("OamUser")->findOneById(1);
        $sess_oam_user_id = $this->session->userdata("sc_oam_user_id");
        if (!$sess_oam_user_id) {
            return false;
        }

        $q = Doctrine_Query::create()->from("OamUser ou")->where("ou.id = ?", $sess_oam_user_id);
        $rows = $q->execute();
        if (!$rows || $rows->count() == 0) {
            return false;
        }

        return $rows[0];
    }

    protected function myUser()
    {
        if (!$this->loggedInUser) {
            $this->loggedInUser = $this->getLoggedinUser();
        }
        return $this->loggedInUser;
    }

    function login()
    {
        $data = array();
        $data["hide_menu"] = true;
        $data["error_msg"] = "";
        $data["email"] = "";

        if ($_POST) {
            $data["email"] = $this->input->post("email");
            $password = $this->encryptPassword($this->input->post("password"));
            $ret = $this->getDBData('SELECT * FROM oam_user WHERE email_address = \'' . $data['email'] . '\' AND password = \'' . $password . '\'');

            if (!$ret) {

                $data["error_msg"] = "Invalid credentials given";
                $this->render("login", $data, false);
            } else {
                $oamUser = $ret[0];
                $this->session->set_userdata("sc_oam_user_id", $oamUser['id']);
                $originalUri = $this->session->userdata("sc_oam_original_uri");
                $this->session->unset_userdata("sc_oam_original_uri");
                $this->loggedInUser = $oamUser;
                redirect($originalUri);
                return;

            }
        } else {
            $this->render("login", $data, false);
        }
    }

    protected function _render($page, &$data = array(), $returnAsString = false)
    {
        $data['subsystem'] = 'admin';
        parent::_render($page, $data, $returnAsString);
    }

    private function getMainMenu($page){
        return isset($this->mainMenuPage[$page])?$this->mainMenuPage[$page]:false;
    }

    private function initRender(&$data, $page){
        if(array_search($page, $this->hideMenuPages)){
            $data['hide_menu']=true;
        }else{
            $data['hide_menu']=false;
        }
        $data['currency'] = $this->currency;
        $data['render_page'] = $page;
        $data["lang_code"] = "en";
        $data["oam_user"] = $this->myUser();
        $data['main_menu'] = $this->getMainMenu($page);
        //		$data["lang"] = $this->languageEntries;
        //		$data["currency"] = $this->liboam->getCurrency();
    }

    protected function render($page, $data = array(), $showSidebar = true)
    {
        $this->initRender($data, $page);
        $this->parser->parse("admin/header.html", $data);
        $this->parser->parse("admin/$page.html", $data);
        if ($showSidebar)
            $this->parser->parse("admin/sidebar.html", $data);
        return;
    }

    function getDBData($query, $returnResult = true, $params = array())
    {
        try {
            $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
            $stmt = $pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            if (!$returnResult)
                return true;
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logException(__FUNCTION__, $e, null, $query);
            return array();
        }
    }

    private function logException($functionName, &$exception, $subscriberId = null, $comment = null)
    {
        _f('Exception at ' . $functionName . ' line ' . $exception->getLine() . ': ' . $exception->getMessage() . '::::' . $comment);
        try {
        } catch (Exception $e) {
            _f("Exception in logException :" . $e->getMessage());
        }
    }

    private function encryptPassword($password)
    {
        return sha1($password); //CAREFUL WHEN CHANGING THIS! WILL AFFECT EXISTING ACCOUNTS!
    }


}
