<?php

namespace CandyCMS\Models;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use PDO;

class Projects extends \CandyCMS\Core\Models\Main {

  public function getId($iId, $bUpdate = false) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        p.*,
                                        UNIX_TIMESTAMP(p.date) as date,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email
                                      FROM " . SQL_PREFIX . "projects p
                                      LEFT JOIN " . SQL_PREFIX . "users u
                                      ON p.author_id=u.id
                                      WHERE p.id = :id
                                      LIMIT 1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      $aRow = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth(__METHOD__ . ' - ' . $p->getMessage());
      exit('SQL error.');
    }

    if ($bUpdate === true)
      $aRow = $this->_formatForUpdate($aRow);
    else {
      $aRow = $this->_formatForOutput($aRow,
                        array('id', 'author_id'),
                        array('published'),
                        'projects');
      // TODO add logo and other images
    }
    return $aRow;
  }

  public function getOverview() {
    try {
      # Show unpublished items and entries with diffent languages to moderators or administrators only
      $sWhere = isset($this->_aSession['user']['role']) && $this->_aSession['user']['role'] >= 3 ?
              '' :
              "WHERE published = '1'";

      $oQuery = $this->_oDb->prepare("SELECT
                                        p.*,
                                        UNIX_TIMESTAMP(p.date) as date,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email,
                                        u.use_gravatar
                                      FROM
                                        " . SQL_PREFIX . "projects p
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        p.author_id=u.id
                                     " . $sWhere . "
                                      ORDER BY
                                        p.date DESC");

      $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth(__METHOD__ . ' - ' . $p->getMessage());
      exit('SQL error.');
    }

    $aData = Array();
    foreach ($aResult as $aRow) {
      $aRowData = $this->_formatForOutput($aRow,
                            array('id', 'user_id', 'author_id'),
                            array('published', 'use_gravatar'),
                            'projects');
      $aRowData['url_createfile'] = '/projects/' . $aRowData['id'] . '/createfile';
      //add thumbnails
      $sUrlUpload = PATH_UPLOAD . '/projects/' . $aRowData['id'];
      $oFinder = new \Symfony\Component\Finder\Finder();
      $oFinder->files()->in($sUrlUpload . '/32');
      foreach ($oFinder as $oFile) {
        $aAr = array();
        foreach (array('32', 'popup', 'original', 'thumb') as $sSize)
          $aAr['url_' . $sSize] = '/' . $sUrlUpload . '/' . $sSize . '/' . $oFile->getFilename();

        $aRowData['thumbnails'][] = $aAr;
      }

      $aData[] = $aRowData;
    }
    return $aData;
 }

  /**
   * Create a projects entry and uploads logos.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function create() {
    $iPublished = isset($this->_aRequest[$this->_sController]['published']) &&
            $this->_aRequest[$this->_sController]['published'] == true ?
            1 :
            0;

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "projects
                                        ( author_id,
                                          title,
                                          content,
                                          url_demo,
                                          url_project,
                                          date,
                                          published)
                                      VALUES
                                        ( :author_id,
                                          :title,
                                          :content,
                                          :url_demo,
                                          :url_project,
                                          NOW(),
                                          :published )");

      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);

      foreach (array('title', 'content', 'url_demo', 'url_project') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput], false),
                PDO::PARAM_STR);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = parent::$_oDbStatic->lastInsertId();

      # Create missing thumb folders.
      $sPath = PATH_UPLOAD . '/projects/' . parent::$iLastInsertId;
      foreach (array('32', 'thumbnail', 'popup', 'original') as $sFolder) {
        if (!is_dir($sPath . '/' . $sFolder))
          mkdir($sPath . '/' . $sFolder, 0755, true);
      }

      return $bReturn;
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth(__METHOD__ . ' - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Update a project entry.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
  public function update($iId) {
    $iPublished = isset($this->_aRequest[$this->_sController]['published']) &&
            $this->_aRequest[$this->_sController]['published'] == true ?
            1 :
            0;

    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "projects
                                      SET
                                        author_id = :author_id,
                                        title = :title,
                                        content = :content,
                                        url_demo = :url_demo,
                                        url_project = :url_project,
                                        published = :published
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);

      foreach (array('title', 'content', 'url_demo', 'url_project') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput], false),
                PDO::PARAM_STR);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth(__METHOD__ . ' - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Delete a projects entry and also delete its logos.
   *
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    $bReturn = parent::destroy($iId);

    if ($bReturn) {
      // TODO forech found file in $sPathO and/or $sPathR
      //  unlink($sPathO . $iId . $sFileP);
      //  unlink($sPathR . $iId . $sFileP);
      
      # Create missing thumb folders.
      $sPath = PATH_UPLOAD . '/projects/' . parent::$iLastInsertId;
      foreach (array('32', 'thumbnail', 'popup', 'original') as $sFolder) {
        if (is_dir($sPath . '/' . $sFolder)) {
          //TODO destroy contained files
          rmdir($sPath . '/' . $sFolder, 0755, true);
        }
      }

      rmdir($sPath);
    }

    return $bReturn;
  }
}
