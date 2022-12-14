<?php

namespace Fir\Controllers;

use Fir\Libraries\Search;
use GuzzleHttp\Client;

class Admin extends Controller {

    /**
     * @var object
     */
    protected $admin;

    public function index() {
        redirect('admin/login');
    }

    public function login() {
        $this->admin = $this->model('Admin');

        $data['menu_view'] = $this->menu();

        // If the user tries to log-in
        if(isset($_POST['login'])) {
            $this->admin->username = $data['username'] = $_POST['username'];
            $this->admin->password = $_POST['password'];

            if(isset($_POST['remember'])) {
                // Generate the remember me token
                $this->setToken();
            }
        }

        // Attempt to auth the user
        $auth = $this->auth();

        // If the user has been logged-in
        if($auth) {
            redirect('admin/dashboard');
        }
        // If the user could not be logged-in
        elseif(isset($_POST['login'])) {
            $_SESSION['message'][] = ['error', $this->lang['invalid_user_pass']];
            $this->logout(false);
        }

        $data['settings_view'] = $this->view->render($data, 'admin/login');

        $data['page_title'] = $this->lang['login'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['login']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function dashboard() {
        $data['menu_view'] = $this->menu();

        $data['settings_view'] = $this->view->render($data, 'admin/dashboard');
        $data['page_title'] = $this->lang['dashboard'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['dashboard']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function general() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        // Save the settings
        if(isset($_POST['submit'])) {
            // Validate the timezone
            $_POST['timezone'] = (in_array($_POST['timezone'], timezone_identifiers_list()) ? $_POST['timezone'] : '');

            // If there's no error during validation
            if(empty($_SESSION['message'])) {
                $this->admin->general($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/general');
        }

        // Get the newly saved settings
        $data['site_settings'] = $this->admin->getSiteSettings();

        $data['settings_view'] = $this->view->render($data, 'admin/general');

        $data['page_title'] = $this->lang['general'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['general']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function search() {
        $search = new Search();
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        // Save the settings
        if(isset($_POST['submit'])) {
            // Basic validation
            $_POST['search_per_ip'] = (int)$_POST['search_per_ip'] >= 0 ? (int)$_POST['search_per_ip'] : 0;
            $_POST['suggestions_per_ip'] = (int)$_POST['suggestions_per_ip'] >= 0 ? (int)$_POST['suggestions_per_ip'] : 0;
            $_POST['search_answers'] = ($_POST['search_answers'] > 0 ? 1 : 0);
            $_POST['search_suggestions'] = ($_POST['search_suggestions'] > 0 ? 1 : 0);
            $_POST['search_related'] = ($_POST['search_related'] > 0 ? 1 : 0);
            $_POST['web_per_page'] = $_POST['web_per_page'] > 49 || $_POST['web_per_page'] < 0 ? 49 : (int)$_POST['web_per_page'];
            $_POST['images_per_page'] = $_POST['images_per_page'] > 149 || $_POST['images_per_page'] < 0 ? 149 : (int)$_POST['images_per_page'];
            $_POST['videos_per_page'] = $_POST['videos_per_page'] > 104 || $_POST['videos_per_page'] < 0 ? 104 : (int)$_POST['videos_per_page'];
            $_POST['news_per_page'] = $_POST['news_per_page'] > 49 || $_POST['news_per_page'] < 0 ? 49 : (int)$_POST['news_per_page'];
            $_POST['search_new_window'] = ($_POST['search_new_window'] > 0 ? 1 : 0);
            $_POST['search_entities'] = (int)$_POST['search_entities'] >= 0 ? (int)$_POST['search_entities'] : 0;
            $_POST['search_privacy'] = ($_POST['search_privacy'] > 0 ? 1 : 0);
            // If all post
            if($_POST['web_per_page'] == 0 && $_POST['images_per_page'] == 0 && $_POST['videos_per_page'] == 0 && $_POST['news_per_page']) {
                $_POST['web_per_page'] = 20;
            }

            if($_POST['search_per_ip'] == 0) {
                $this->admin->deleteSearchLimit();
            }

            // If there's no error during validation
            if(empty($_SESSION['message'])) {
                $this->admin->search($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/search');
        }

        // Get the newly saved settings
        $data['site_settings'] = $this->admin->getSiteSettings();

        $data['markets'] = $search->getMarkets();

        $data['settings_view'] = $this->view->render($data, 'admin/search');

        $data['page_title'] = $this->lang['search'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['search']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function appearance() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        // Save the settings
        if(isset($_POST['submit'])) {
            $_POST['site_backgrounds'] = (int)$_POST['site_backgrounds'] >= 0 ? (int)$_POST['site_backgrounds'] : 0;
            $_POST['site_dark_mode'] = (int)$_POST['site_dark_mode'] >= 0 ? (int)$_POST['site_dark_mode'] : 0;
            $_POST['site_center_content'] = (int)$_POST['site_center_content'] >= 0 ? (int)$_POST['site_center_content'] : 0;

            $fields = ['logo_small', 'logo_small_dark', 'logo_large', 'logo_large_dark', 'favicon'];
            foreach($_FILES as $key => $value) {
                // Validate the input names
                if(in_array($key, $fields)) {
                    if(!empty($_FILES[$key]['name'])) {
                        $fileFormat = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                        // If there is no error during upload and the file is PNG
                        if($_FILES[$key]['error'] == 0 && in_array($fileFormat, ['png', 'svg'])) {
                            $fileName = $key.'.'.$fileFormat;
                            // If the file can't be written on the disk (will return 0)

                            $path = sprintf('%s/../../%s/%s/brand/', __DIR__, PUBLIC_PATH, UPLOADS_PATH);
                            if(move_uploaded_file($_FILES[$key]['tmp_name'], $path.$fileName) == false) {
                                $_SESSION['message'][] = ['error', sprintf($this->lang['upload_error_code'], $_FILES[$key]['error'])];
                            } else {
                                // Get the old image
                                $oldFileName = $this->settings[$key] ?? null;

                                // Remove the old variant of the image
                                if($oldFileName && $oldFileName != $fileName) {
                                    unlink($path.$oldFileName);
                                }
                                $this->admin->insertUpdate($key, $fileName);
                            }
                        } else {
                            $_SESSION['message'][] = ['error', sprintf($this->lang['upload_error_code'], $_FILES[$key]['error'])];
                        }
                    }
                }
            }

            // If there's no error during validation
            if(empty($_SESSION['message'])) {
                $this->admin->appearance($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/appearance');
        }

        // Get the newly saved settings
        $data['site_settings'] = $this->admin->getSiteSettings();

        $data['settings_view'] = $this->view->render($data, 'admin/appearance');

        $data['page_title'] = $this->lang['appearance'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['appearance']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function ads() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        // Save the settings
        if(isset($_POST['submit'])) {
            // If there's no error during validation
            if(empty($_SESSION['message'])) {
                $_POST['ads_safe'] = ($_POST['ads_safe'] > 0 ? 1 : 0);
                $this->admin->ads($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/ads');
        }

        // Get the newly saved settings
        $data['site_settings'] = $this->admin->getSiteSettings();

        $data['settings_view'] = $this->view->render($data, 'admin/ads');

        $data['page_title'] = $this->lang['ads'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['ads']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function password() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        // Save the settings
        if(isset($_POST['submit'])) {
            // If current password entered is invalid
            if(password_verify($_POST['current_password'], $_SESSION['adminPassword']) == false) {
                $_SESSION['message'][] = ['error', $this->lang['wrong_current_password']];
            }

            // If new password doesn't match
            if($_POST['password'] != $_POST['repeat_password']) {
                $_SESSION['message'][] = ['error', $this->lang['password_not_matching']];
            }

            // If password is too short
            if(strlen($_POST['password']) < 8) {
                $_SESSION['message'][] = ['error', $this->lang['password_too_short']];
            }

            // If there's no error during validation
            if(empty($_SESSION['message'])) {
                $this->setPassword($_POST['password']);
                $_POST['password'] = $_SESSION['adminPassword'];

                if($_SESSION['adminRemember']) {
                    $this->setToken();
                    $this->admin->renewToken();
                }

                $this->admin->password($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/password');
        }

        $data['settings_view'] = $this->view->render($data, 'admin/password');

        $data['page_title'] = $this->lang['password'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['password']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function themes() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        $data['themes'] = $this->getThemes();

        // Save the settings
        if(isset($_POST['theme'])) {
            $availableThemes = array_keys($data['themes']);

            // Verify if the theme exists
            if(in_array($_POST['theme'], $availableThemes)) {
                $this->admin->setTheme($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/themes');
        }

        $data['settings_view'] = $this->view->render($data, 'admin/themes');

        $data['page_title'] = $this->lang['themes'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['themes']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function languages() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        $data['languages'] = $this->getLanguages();
        // Save the settings
        if(isset($_POST['language'])) {
            $availableLanguages = array_keys($data['languages']);

            // Verify if the language exists
            if(in_array($_POST['language'], $availableLanguages)) {
                $this->admin->setLanguage($_POST);

                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/languages');
        }

        $data['settings_view'] = $this->view->render($data, 'admin/languages');

        $data['page_title'] = $this->lang['languages'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['languages']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    public function info_pages() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        $data['menu_view'] = $this->menu();

        $data['info_pages'] = $this->admin->getInfoPages();
        $data['page_title'][] = $this->lang['info_pages'];

        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['info_pages']];

        // Edit Page
        if(isset($this->url[2]) && $this->url[2] == 'edit') {
            $page = $this->admin->getInfoPage($this->url[3], 0);

            // If the page requested exists
            if(isset($page['id'])) {
                // Form switcher
                $data['form_for'] = 1;
                $data['info_page'] = $page;
                $data['page_title'][0] = $this->lang['edit'];
                $data['page_title'][] = $data['info_pages'][$page['id']]['title'];

                if(isset($_POST['submit'])) {
                    $this->validateInfoPage($page, 1);

                    if(empty($_SESSION['message'])) {
                        $this->admin->updateInfoPage($_POST);
                        $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
                    }

                    redirect('admin/info_pages/edit/'.$page['id']);
                }

                $this->view->metadata['title'][] = $this->lang['edit'];
                $this->view->metadata['title'][] = $page['title'];
                $data['settings_view'] = $this->view->render($data, 'admin/info_pages_form');
            } else {
                redirect('admin/info_pages');
            }
        }
        // Delete Page
        elseif(isset($this->url[2]) && $this->url[2] == 'delete') {
            $page = $this->admin->getInfoPage($this->url[3], 0);

            // If the page requested exists
            if(isset($page['id'])) {
                $data['info_page'] = $page;
                $data['page_title'][0] = $this->lang['delete'];
                $data['page_title'][] = $data['info_pages'][$page['id']]['title'];

                if(isset($_POST['submit'])) {
                    $_POST['page_id'] = $page['id'];

                    if(empty($_SESSION['message'])) {
                        $this->admin->deleteInfoPage($_POST);
                        $_SESSION['message'][] = ['success', sprintf($this->lang['page_deleted'], $page['title'])];
                        redirect('admin/info_pages');
                    }

                    redirect('admin/info_pages/edit/'.$page['id']);
                }

                $this->view->metadata['title'][] = $this->lang['delete'];
                $this->view->metadata['title'][] = $page['title'];
                $data['settings_view'] = $this->view->render($data, 'admin/info_pages_delete');
            } else {
                redirect('admin/info_pages');
            }
        }
        // New Page
        elseif(isset($this->url[2]) && $this->url[2] == 'new') {
            // Form Switcher
            $data['form_for'] = 0;
            // This variable is required to default on the public setting
            $data['info_page']['public'] = 1;
            $data['page_title'][0] = $this->lang['new_page'];

            if(isset($_POST['submit'])) {
                $page = $this->admin->getInfoPage($_POST['page_url'], 1);
                $this->validateInfoPage($page, 0);

                if(empty($_SESSION['message'])) {
                    $this->admin->addInfoPage($_POST);
                    $_SESSION['message'][] = ['success', sprintf($this->lang['page_created'], $_POST['page_title'])];
                    redirect('admin/info_pages');
                }

                redirect('admin/info_pages/new');
            }

            $this->view->metadata['title'][] = $this->lang['new_page'];
            $data['settings_view'] = $this->view->render($data, 'admin/info_pages_form');
        }
        // List Pages
        else {
            $data['settings_view'] = $this->view->render($data, 'admin/info_pages');
        }
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Validate Info Page when creating or updating one
     *
     * @param   array   $page   The Info Page to be validated
     * @param   int     $type   The Validation Type, 1 for Update, 0 for New
     */
    private function validateInfoPage($page, $type) {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        if($type) {
            $checkPage = $this->admin->getInfoPage($_POST['page_url'], 1);

            // Variable used in various model methods
            $_POST['page_id'] = $page['id'];
        }

        $_POST['page_title'] = substr(strip_tags($_POST['page_title']), 0, 64);
        $_POST['page_url'] = filter_var(substr(htmlspecialchars(strip_tags($_POST['page_url'])), 0, 64), FILTER_SANITIZE_URL);
        $_POST['page_public'] = ($_POST['page_public'] == 1 ? 1 : 0);

        // Check if any field is empty
        if(empty($_POST['page_title']) || empty($_POST['page_url']) || empty($_POST['page_content'])) {
            $_SESSION['message'][] = ['error', $this->lang['all_fields_required']];
        }

        // Check if the page name already exists (exclude the current page)
        if($type) {
            if($_POST['page_url'] == $checkPage['url'] && $this->url[3] != $checkPage['id']) {
                $_SESSION['message'][] = ['error', sprintf($this->lang['page_url_exists'], $_POST['page_url'])];
            }
        } else {
            if(isset($page['url'])) {
                $_SESSION['message'][] = ['error', sprintf($this->lang['page_url_exists'], $_POST['page_url'])];
            }
        }
    }

    /**
     * Logout the Admin User or clear the credentials if they become outdated
     *
     * @param   bool    $redirect   Redirect the user after it has been logged out
     */
    public function logout($redirect = true) {
        unset($_SESSION['adminUsername']);
        unset($_SESSION['adminPassword']);
        unset($_SESSION['isAdmin']);
        setcookie("adminUsername", '', time()-3600, COOKIE_PATH);
        setcookie("adminToken", '', time()-3600, COOKIE_PATH);

        if($redirect) {
            // Unset the token id in order to be refreshed only when Logging Out
            unset($_SESSION['token_id']);

            redirect('admin/login');
        }
    }

    /**
     * The Admin Panel menu
     *
     * @return  string
     */
    private function menu() {
        // Array Map: Key(Menu Elements) => Array(Bold, Not Dynamic tag)
        if(isset($_SESSION['isAdmin'])) {
            $data['menu'] = [
                'dashboard'     => [false, false],
                'general'       => [false, false],
                'search'        => [false, false],
                'appearance'    => [false, false],
                'themes'        => [false, false],
                'languages'     => [false, false],
                'info_pages'    => [false, false],
                'ads'           => [false, false],
                'password'      => [false, false],
                'logout'        => [false, true]
            ];
        } else {
            $data['menu'] = [
                'login'         => [false, false],
            ];
        }

        // If on the current route, enable the Bold flag
        if (array_key_exists($this->url[1], $data['menu'])) {
            $data['menu'][$this->url[1]][0] = true;
        }

        return $this->view->render($data, 'admin/menu');
    }

    /**
     * Get the available Languages
     */
    private function getLanguages() {
        $path = sprintf('%s/../languages/', __DIR__, PUBLIC_PATH, THEME_PATH);

        $languages = scandir($path);

        $output = [];
        foreach($languages as $language) {
            // Select only the .php files
            if($language != '.' && $language != '..' && substr($language, -4, 4) == '.php') {
                $language = substr($language, 0, -4);
                // Store the language information
                require($path.$language.'.php');
                $output[$language]['name'] = $name;
                $output[$language]['author'] = $author;
                $output[$language]['url'] = $url;
                $output[$language]['path'] = $language;
            }
        }

        return $output;
    }

    /**
     * Get the available Themes
     */
    private function getThemes() {
        $path = sprintf('%s/../../%s/%s/', __DIR__, PUBLIC_PATH, THEME_PATH);

        $themes = scandir($path);

        $output = [];
        foreach($themes as $theme) {
            // Check if the theme has an info.php file a && file_exists($path.$theme.'/icon.png)nd a thumbnail
            if(file_exists($path.$theme.'/info.php') && file_exists($path.$theme.'/icon.png')) {
                // Store the theme information
                require($path.$theme.'/info.php');
                $output[$theme]['name']     = $name;
                $output[$theme]['author']   = $author;
                $output[$theme]['url']      = $url;
                $output[$theme]['version']  = $version;
                $output[$theme]['path']     = $theme;
            }
        }

        return $output;
    }

    /**
     * Check whether the user can be authed or not
     *
     * @return	array | bool
     */
    private function auth() {
        // If the user has previously been authenticated
        if(isset($_SESSION['adminUsername']) && isset($_SESSION['adminPassword'])) {
            $this->admin->username = $_SESSION['adminUsername'];
            $this->admin->password = $_SESSION['adminPassword'];
            $auth = $this->admin->get(1);

            if($this->admin->password == $auth['password']) {
                $logged = true;
            }
        }
        // If the user has long term login enabled
        elseif(isset($_COOKIE['adminUsername']) && isset($_COOKIE['adminToken'])) {
            $this->admin->username = $_COOKIE['adminUsername'];
            $this->admin->rememberToken = $_COOKIE['adminToken'];
            $auth = $this->admin->get(2);

            if($this->admin->rememberToken == $auth['remember_token'] && !empty($auth['remember_token'])) {
                $_SESSION['adminUsername'] = $this->admin->username;
                $this->setPassword($auth['password']);
                $logged = true;
            }
        }
        // If the user is authenticating
        else {
            $auth = $this->admin->get(0);

            // Set the sessions
            $_SESSION['adminUsername'] = $this->admin->username;
            $this->setPassword($this->admin->password);

            if(isset($auth['password']) && password_verify($this->admin->password, $auth['password'])) {
                if($this->admin->rememberToken) {
                    $this->admin->renewToken();
                }
                session_regenerate_id();
                $logged = true;
            }
        }

        if(isset($logged)) {
            $_SESSION['isAdmin'] = true;
            return $auth;
        }

        return false;
    }

    /**
     * @param   string  $password
     */
    private function setPassword($password) {
        $_SESSION['adminPassword'] = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Set the remember me Cookie tokens
     */
    private function setToken() {
        $this->admin->rememberToken = password_hash($this->admin->username.generateSalt().time().generateSalt(), PASSWORD_DEFAULT);

        setcookie("adminUsername", $this->admin->username, time() + 30 * 24 * 60 * 60, COOKIE_PATH, null, 1);
        setcookie("adminToken", $this->admin->rememberToken, time() + 30 * 24 * 60 * 60, COOKIE_PATH, null, 1);

        $_SESSION['adminRemember'] = true;
    }
}