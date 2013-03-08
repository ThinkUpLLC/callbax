<?php

class InstallationListController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        $installation_dao = new InstallationMySQLDAO();
        $user_dao = new UserMySQLDAO();

        $total_installations = $installation_dao->getTotal();
        $total_users = $user_dao->getTotal();

        $total_active_installations = $installation_dao->getTotalActive();
        $total_active_users = $user_dao->getTotalActive();

        $page = isset($_GET['page'])?$_GET["page"]:1;
        $limit = isset($_GET['limit'])?$_GET['limit']:100;
        if (isset($_GET['l']) && $_GET['l'] != 'active') {
            $installations_page = $installation_dao->getPage($page, $limit);
        } else {
            $installations_page = $installation_dao->getPageActiveInstallations($page, $limit);
        }

        $this->addToView('installations', $installations_page['installations']);
        $this->addToView('next_page', $installations_page['next_page']);
        $this->addToView('prev_page', $installations_page['prev_page']);

        $this->addToView('total_installations', $total_installations);
        $this->addToView('total_users', $total_users);
        $this->addToView('total_active_installations', $total_active_installations);
        $this->addToView('total_active_users', $total_active_users);
        $this->addToView('total_optouts', $installation_dao->getTotalOptOuts());
        $this->addToView('first_seen_installation_date', $installation_dao->getFirstSeenInstallationDate());

        $this->addToView('service_stats', $user_dao->getServiceTotals());
        $this->addToView('version_stats', $installation_dao->getVersionTotals());
        $this->addToView('usercount_stats', $installation_dao->getUserCountDistribution());
        $this->addToView('host_stats', $installation_dao->getHostingProviderDistribution($total_installations));
        return $this->generateView();
    }
}
