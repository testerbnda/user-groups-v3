<?php

namespace Modules\Admin\Repositories\Interfaces;
use Modules\Admin\Entities\Sites;
interface SitesInterface
{

    /**
     * Get new instance
     */
    public function save(Sites $sites, array $data);
    public function update(Sites $sites, array $data);
    public function firstornew($id);
    public function ajaxgetlist();
    public function siteslist();
    public function sitesRoles();
    public function getpayinout($type);
    public function siteBydomain($domain);
    public function getrolesbysite($site_id);
    public function ajaxgetsitesinvoices();
    public function monthly_invoice_generate($data);
    public function getSiteConfig($id);
    public function getColorThemeById($id);
    public function getColorThemes();
    public function getSiteInvoices($id);
    public function getSiteAgreements($id);
    public function ajaxgetsitedeals($id);
    public function getSiteVirtualAccInfo($id);
}
