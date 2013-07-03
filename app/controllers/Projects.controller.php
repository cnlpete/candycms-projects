<?php

namespace CandyCMS\Controllers;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Helpers\Upload;
use CandyCMS\Core\Helpers\Image;
use CandyCMS\Core\Controllers\Logs;

class Projects extends \CandyCMS\Core\Controllers\Main {

  protected function _show() {
    return "";
  }

  protected function _overview() {
    $oTemplate = $this->oSmarty->getTemplate($this->_sController, 'overview');
    $this->oSmarty->setTemplateDir($oTemplate);

    if (!$this->oSmarty->isCached($oTemplate, UNIQUE_ID))
      $this->oSmarty->assign('projects', $this->_oModel->getOverview());

    $sTitle = I18n::get('global.projects');
    $this->setTitle($sTitle);

    # add rss info
    $this->_aRSSInfo[] = array(
                            'url' => WEBSITE_URL . '/projects.rss',
                            'title' => $sTitle);


    return $this->oSmarty->fetch($oTemplate, UNIQUE_ID);
  }

  protected function _overviewRSS() {
    $oTemplate =  $this->oSmarty->getTemplate($this->_sController, 'overviewRSS');
    $this->oSmarty->setTemplateDir($oTemplate);

    if (!$this->oSmarty->isCached($oTemplate, UNIQUE_ID)) {
      $this->_aData = $this->_oModel->getOverview();

      $this->oSmarty->assign('data', $this->_aData);
      $this->oSmarty->assign('_WEBSITE', array(
          'title' => I18n::get('global.projects'),
          'date'  => date('D, d M Y H:i:s O', time())
      ));
    }

    return $this->oSmarty->fetch($oTemplate, UNIQUE_ID);
  }

  protected function _showFormTemplate() {
    $this->setTitle(I18n::get('projects.title.'.$this->_aRequest['action']));
    $this->oSmarty->setCaching(false);
    return parent::_showFormTemplate();
  }

  protected function _showFormFileTemplate() {
    $oTemplate = $this->oSmarty->getTemplate($this->_sController, '_form_image');
    $this->oSmarty->setTemplateDir($oTemplate);
    $this->oSmarty->setCaching(false);
    foreach ($this->_aRequest[$this->_sController] as $sInput => $sData)
      $this->oSmarty->assign($sInput, $sData);

    $this->setTitle(I18n::get('projects.files.title.create'));

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    return $this->oSmarty->fetch($oTemplate, UNIQUE_ID);
  }

  protected function _create() {
    $this->_setError('title');
    $this->_setError('content');

    $this->_sRedirectURL = '/' . $this->_sController;

    return parent::_create();
  }

  protected function _update() {
    $this->_setError('title');
    $this->_setError('content');
    
    $this->_sRedirectURL = '/' . $this->_aRequest['controller'];

    return parent::_update();
  }

  public function createFile() {
    $this->setTitle(I18n::get('projects.files.title.create'));
    $this->oSmarty->setCaching(false);

    if ($this->_aSession['user']['role'] < 3)
      return Helper::redirectTo('/errors/401');

    return isset($this->_aRequest[$this->_sController]) ||
            isset($this->_aRequest['type']) && 'json' == $this->_aRequest['type'] ?
            $this->_createFile() :
            $this->_showFormFileTemplate();
  }

  protected function _createFile() {
    $this->_setError('cut');
    $this->_setError('file');

    require_once PATH_STANDARD . '/vendor/candycms/core/helpers/Upload.helper.php';

    if ($this->_aError)
      return $this->_showFormFileTemplate();

    else {
      $oUploadFile = new Upload($this->_aRequest, $this->_aSession, $this->_aFile);

      try {
        $aReturnValues = $oUploadFile->uploadGalleryFiles('projects');

        $aIds   = $oUploadFile->getIds(false);
        $aExts  = $oUploadFile->getExtensions();

        $iFileCount = count($aReturnValues);
        $bReturnValue = $iFileCount > 0;
        for ($iI = 0; $iI < $iFileCount; $iI++)
          if (!$aReturnValues[$iI])
            $bReturnValue = false;

        for ($iI = 0; $iI < $iFileCount; $iI++)
          Logs::insert( $this->_sController,
                        'createfile',
                        (int) $this->_iId,
                        $this->_aSession['user']['id'],
                        '', '', $bReturnValue);

        if ($bReturnValue) {
          $this->oSmarty->clearControllerCache($this->_sController);

          return Helper::successMessage(I18n::get('success.file.upload'),
                  '/' . $this->_sController,
                  $this->_aRequest);
        }
        else
          return Helper::errorMessage(I18n::get('error.file.upload'),
                  '/' . $this->_sController . '/' . $this->_iId . '/createfile',
                  $this->_aRequest);
      }
      catch (AdvancedException $e) {
        AdvancedException::reportBoth(__METHOD__ . ' - ' . $e->getMessage());
        return Helper::errorMessage($e->getMessage(),
                '/' . $this->_sController . '/' . $this->_iId . '/createfile',
                $this->_aRequest);
      }
    }
  }

  public function destroyFile() {
    $this->oSmarty->setCaching(false);
    return $this->_aSession['user']['role'] < 3 ?
            Helper::redirectTo('/errors/401') :
            $this->_destroyFile();
  }

  protected function _destroyFile() {
    $aDetails = $this->_oModel->getFileData($this->_iId);
    $bReturn  = $this->_oModel->destroyFile($this->_iId) === true;

    Logs::insert( $this->_sController,
                  $this->_aRequest['action'],
                  (int) $this->_iId,
                  $this->_aSession['user']['id'],
                  '', '', $bReturn);

    if ($bReturn) {
      $this->oSmarty->clearControllerCache($this->_sController);

      unset($this->_iId);
      return Helper::successMessage(I18n::get('success.destroy'), '/' . $this->_sController . '/' .
              $aDetails['album_id']);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_sController . '/' .
              $aDetails['album_id']);
  }
}
