<?php
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Base\DataBase;

use FacturaScripts\Dinamic\Model\TicketCustomLine;

class PrintTicketSettings extends Controller
{
    public $headerLines;
    public $footerLines;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion de tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $appSettings = new AppSettings();

        $footerText = $this->request->request->get('footertext');
        if ($footerText) {
            $appSettings->set('ticket', 'footertext', $footerText);
        }

        $lineLength = $this->request->request->get('linelength');
        if ($lineLength) {
            $appSettings->set('ticket', 'linelength', $lineLength);
        }

        $printPrice = $this->request->request->get('printprice');
        if ($lineLength) {
            $appSettings->set('ticket', 'printprice', $printPrice);
        }
        $appSettings->save();

        $action = $this->request->request->get('accion');

        switch ($action) {
            case 'save':
                $this->saveCustomLines();
                break;

            case 'delete':
                $this->deleteCustomLine();
                break;
            
            default:
                # code...
                break;
        }

        $this->headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
        $this->footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');
    }

    public function saveCustomLines()
    {
        $customLineID = $this->request->request->get('idlinea');
        $customLinePosition = $this->request->request->get('posicion');
        $customLineText = $this->request->request->get('texto');

        $line = new TicketCustomLine();
        $line->loadFromCode($customLineID);
        
        $line->documento = 'general';
        $line->posicion = $customLinePosition;
        $line->texto = $customLineText;
        $line->save();
    }

    public function deleteCustomLine()
    {
        $customLineID = $this->request->request->get('idlinea');

        $customLine = new TicketCustomLine();
        if ($customLine->loadFromCode($customLineID)) {
            $customLine->delete();
        }
    }
}