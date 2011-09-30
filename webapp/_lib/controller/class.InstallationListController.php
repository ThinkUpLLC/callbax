<?php

class InstallationListController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        $installation_dao = new InstallationMySQLDAO();
        $user_dao = new UserMySQLDAO();

        $total_installations = $installation_dao->getTotal();
        //for now, show single page of all installations
        $installations_page = $installation_dao->getAll();

        $this->addToView('installations', $installations_page['installations']);
        //        $this->addToView('next_page', $installations_page['next_page']);
        //        $this->addToView('prev_page', $installations_page['prev_page']);

        $this->addToView('total_installations', $total_installations);

        $this->addToView('first_seen_installation_date', $installation_dao->getFirstSeenInstallationDate());

        $this->addToView('service_stats', $user_dao->getServiceTotals());
        $this->addToView('version_stats', $installation_dao->getVersionTotals());
        $this->addToView('usercount_stats', $installation_dao->getUserCountDistribution());
        return $this->generateView();
    }
}
