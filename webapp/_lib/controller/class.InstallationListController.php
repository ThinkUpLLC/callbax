<?php

class InstallationListController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        $installation_dao = new InstallationMySQLDAO();
        $user_dao = new UserMySQLDAO();

        $page = (isset($_GET['p']))?$_GET['p']:1;
        $limit = (isset($_GET['l']))?$_GET['l']:50;

        $installations_page = $installation_dao->getPage($page, $limit);

        $this->addToView('installations', $installations_page['installations']);
        $this->addToView('next_page', $installations_page['next_page']);
        $this->addToView('prev_page', $installations_page['prev_page']);

        $this->addToView('total_installations', $installation_dao->getTotal());

        $this->addToView('first_seen_installation_date', $installation_dao->getFirstSeenInstallationDate());

        $this->addToView('service_stats', $user_dao->getServiceTotals());
        $this->addToView('version_stats', $installation_dao->getVersionTotals());
        return $this->generateView();
    }
}
