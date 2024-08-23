<?php

namespace Modules\Admin\Services;

use Exception;
use Modules\Core\Helpers\Logger;
use Modules\Admin\Repositories\Interfaces\SitesInterface;

class SitesService
{

    private $repository;

    public function __construct(SitesInterface $repository)
    {
        $this->repository = $repository;
    }

    public function ajaxgetlist()
    {
        try {
            return $this->repository->ajaxgetlist();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function siteslist()
    {
        try {
            return $this->repository->siteslist();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function firstornew($id = null)
    {
        try {
            return $this->repository->firstornew($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function siteBydomain($domain = null)
    {
        try {
            return $this->repository->siteBydomain($domain);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function save($sites, $data)
    {
        try {
            return $this->repository->save($sites, $data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function update($sites, $data)
    {
        try {
            return $this->repository->update($sites, $data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function sitesRoles()
    {
        try {
            return $this->repository->sitesRoles();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getpayinout($type = null)
    {
        try {
            return $this->repository->getpayinout($type);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getrolesbysite($site_id = null)
    {
        try {
            return $this->repository->getrolesbysite($site_id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function ajaxgetsitesinvoices()
    {
        try {
            return $this->repository->ajaxgetsitesinvoices();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function monthly_invoice_generate($data)
    {
        try {
            return $this->repository->monthly_invoice_generate($data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getSiteConfig($id)
    {
        try {
            return $this->repository->getSiteConfig($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getColorThemeById($id)
    {
        try {
            return $this->repository->getColorThemeById($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getColorThemes()
    {
        try {
            return $this->repository->getColorThemes();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getSiteInvoices($id)
    {
        try {
            return $this->repository->getSiteInvoices($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }    

    public function getSiteAgreements($id)
    {
        try {
            return $this->repository->getSiteAgreements($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function ajaxgetsitedeals($id)
    {
        try {
            return $this->repository->ajaxgetsitedeals($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function getSiteVirtualAccInfo($id)
    {
        try {
            return $this->repository->getSiteVirtualAccInfo($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
}
