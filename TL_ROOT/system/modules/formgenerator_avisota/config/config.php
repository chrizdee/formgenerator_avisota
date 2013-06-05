<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

// Register hooks
$GLOBALS['TL_HOOKS']['processFormData'][] = array('formgeneratorAvisota', 'sendDoubleOptInMail');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('formgeneratorAvisota', 'replaceInsertTags');

?>