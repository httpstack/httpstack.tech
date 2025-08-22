<?php

namespace HttpStack\App\Models;

use Stringable;
use HttpStack\Model\AbstractModel;
use HttpStack\Datasource\Contracts\CRUD;
use HttpStack\App\Datasources\FS\XmlFile;

/**
 * ViewModel class.
 * Represents data for a specific database table, providing structured access
 * and interaction via a CRUD datasource.
 */
class ViewModel extends AbstractModel
{
    protected $dataSource;
    protected $data = [];

    public function __construct(XmlFile $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->prepareData();
    }

    public function prepareData()
    {
        $arrXml = $this->dataSource->read(); // Returns an associative array
        $arrTemp = [];

        // Flatten header
        if (isset($arrXml['header']) && is_array($arrXml['header'])) {
            foreach ($arrXml['header'] as $strKey => $mixValue) {
                $arrTemp[$strKey] = is_scalar($mixValue) ? $mixValue : null;
            }
        }

        // Flatten intro
        if (isset($arrXml['intro'])) {
            $arrTemp['intro'] = is_scalar($arrXml['intro'])
                ? trim($arrXml['intro'])
                : null;
        }

        // Prepare CTAs
        $arrTemp['ctas'] = [];
        if (isset($arrXml['ctas']['cta']) && is_array($arrXml['ctas']['cta'])) {
            foreach ($arrXml['ctas']['cta'] as $arrCta) {
                $arrCtaTemp = [];
                foreach ($arrCta as $strKey => $mixValue) {
                    if ($strKey === 'link' && is_array($mixValue)) {
                        $arrCtaTemp['link_label'] = $mixValue['label'] ?? '';
                        $arrCtaTemp['link_uri']   = $mixValue['uri'] ?? '';
                    } else {
                        $arrCtaTemp[$strKey] = is_scalar($mixValue) ? $mixValue : null;
                    }
                }
                $arrTemp['ctas'][] = $arrCtaTemp;
            }
        }

        $this->data = $arrTemp;
    }


    public function getAll(): array
    {
        return $this->data;
    }

    public function setAll(array $data): void
    {
        $this->data = $data;
    }
}
