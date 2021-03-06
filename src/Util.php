<?php


declare(strict_types=1);

use Greenter\Data\DocumentGeneratorInterface;
use Greenter\Data\GeneratorFactory;
use Greenter\Data\SharedStore;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Response\CdrResponse;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\Report\XmlUtils;
use Greenter\See;

final class Util
{
    //use StoreTrait;

    private static $current;

    private function __construct()
    {
        $this->shared = new SharedStore();
    }

    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }

        return self::$current;
    }

    /**
     * @param string $endpoint
     * @return See
     */
    public function getSee(?string $endpoint)
    {
        $see = new See();
        $see->setService($endpoint);
//        $see->setCodeProvider(new XmlErrorCodeProvider());
        $certificate = file_get_contents(__DIR__ . '/../resources/cert.pem');
        if ($certificate === false) {
            throw new Exception('No se pudo cargar el certificado');
        }
        $see->setCertificate($certificate);
        /**
         * Clave SOL
         * Ruc     = 20000000001
         * Usuario = MODDATOS
         * Clave   = moddatos
         */
        $see->setClaveSOL('20123456789', 'MODDATOS', 'moddatos');
        $see->setCachePath(__DIR__ . '/../cache');

        return $see;
    }
   

    public function getErrorResponse(\Greenter\Model\Response\Error $error): string
    {
        $result = <<<HTML
        <h2 class="text-danger">Error:</h2><br>
        <b>Código:</b>{$error->getCode()}<br>
        <b>Descripción:</b>{$error->getMessage()}<br>
HTML;

        return $result;
    }

    public function getResponseFromCdr(CdrResponse $cdr)
    {
        $result = <<<HTML
        <h2>Respuesta SUNAT:</h2><br>
        <b>ID:</b> {$cdr->getId()}<br>
        <b>CODE:</b>{$cdr->getCode()}<br>
        <b>DESCRIPTION:</b>{$cdr->getDescription()}<br>
HTML;

        return $result;
    }

    public function writeXmlSinFirmar($nameXml, $xml)
    {
        $this->writeFileXMLSinFirmado($nameXml.'.xml', $xml);
    }

    public function writeXml(DocumentInterface $document, $xml)
    {
        $this->writeFileXMLFirmado($document->getName().'.xml', $xml);
    }

    public function writeCdr(DocumentInterface $document, $zip)
    {
        $this->writeFileCDR('R-'.$document->getName().'.zip', $zip);
    }

    public function writeFileXMLSinFirmado($filenam, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        file_put_contents(__DIR__ . '/../admin/FE/XML/'.$filenam, $content);
    }

    public function writeFileXMLFirmado($filenam, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        file_put_contents(__DIR__ . '/../admin/FE/XMLFIRMADOS/'.$filenam, $content);
    }

    public function writeFileCDR($filenam, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        file_put_contents(__DIR__ . '/../admin/FE/CDR/'.$filenam, $content);
    }

    public function writeFile($filenam, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        file_put_contents(__DIR__ . '/../files/'.$filenam, $content);
    }

    public function getPdf(DocumentInterface $document)
    {
        $html = new HtmlReport('', [
            'cache' => __DIR__ . '/../cache',
            'strict_variables' => true,
        ]);
        $template = $this->getTemplate($document);
        if ($template) {
            $html->setTemplate($template);
        }

        $render = new PdfReport($html);
        $render->setOptions( [
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
            'footer-html' => __DIR__.'/../resources/footer.html',
        ]);
        $binPath = self::getPathBin();
        if (file_exists($binPath)) {
            $render->setBinPath($binPath);
        }
        $hash = $this->getHash($document);
        $params = self::getParametersPdf();
        $params['system']['hash'] = $hash;
        $params['user']['footer'] = '<div>consulte en <a href="https://github.com/giansalex/sufel">sufel.com</a></div>';

        $pdf = $render->render($document, $params);

        if ($pdf === false) {
            $error = $render->getExporter()->getError();
            echo 'Error: '.$error;
            exit();
        }

        // Write html
        $this->writeFile($document->getName().'.html', $render->getHtml());

        return $pdf;
    }

    public static function generator($item, $count)
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $item;
        }

        return $items;
    }

    public function showPdf($content, $filename)
    {
        $this->writeFile($filename, $content);
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($content));

        echo $content;
    }

    public static function getPathBin()
    {
        $path = __DIR__.'/../vendor/bin/wkhtmltopdf';
        if (self::isWindows()) {
            $path .= '.exe';
        }

        return $path;
    }

    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function inPath($command) {
        $whereIsCommand = self::isWindows() ? 'where' : 'which';

        $process = proc_open(
            "$whereIsCommand $command",
            array(
                0 => array("pipe", "r"), //STDIN
                1 => array("pipe", "w"), //STDOUT
                2 => array("pipe", "w"), //STDERR
            ),
            $pipes
        );
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            return $stdout != '';
        }

        return false;
    }

    private function getTemplate($document)
    {
        $className = get_class($document);

        switch ($className) {
            case \Greenter\Model\Retention\Retention::class:
                $name = 'retention';
                break;
            case \Greenter\Model\Perception\Perception::class:
                $name = 'perception';
                break;
            case \Greenter\Model\Despatch\Despatch::class:
                $name = 'despatch';
                break;
            case \Greenter\Model\Summary\Summary::class:
                $name = 'summary';
                break;
            case \Greenter\Model\Voided\Voided::class:
            case \Greenter\Model\Voided\Reversion::class:
                $name = 'voided';
                break;
            default:
                return '';
        }

        return $name.'.html.twig';
    }

    private function getHash(DocumentInterface $document)
    {
        $see = $this->getSee('');
        $xml = $see->getXmlSigned($document);

        $hash = (new \Greenter\Report\XmlUtils())->getHashSign($xml);

        return $hash;
    }

    private static function getParametersPdf()
    {
        $logo = file_get_contents(__DIR__.'/../resources/logo.png');

        return [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header' => 'Telf: <b>(056) 123375</b>',
                'extras' => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => 'GITHUB SELLER'],
                ],
            ]
        ];
    }
}