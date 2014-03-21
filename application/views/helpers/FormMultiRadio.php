<?php

/* 
 * From http://pastebin.com/f1dd448e3
 */

class View_Helper_FormMultiRadio extends Zend_View_Helper_FormElement {
  protected $_inputType = 'radio';
  protected $_isArray = false;
  protected $_filter;
  protected $_labelAttribs;
  protected $_labelPlacement;
  protected $_endTag;
  protected $_id;
  protected $_name;
  protected $_attribs;
 
  public function formMultiRadio($name, $value = null, $attribs = null,
      $options = null, $listsep = "<br />\n")
  {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable
 
    // retrieve attributes for labels (prefixed with 'label_' or 'label')
    $this->_label_attribs = array();
    foreach ($attribs as $key => $val) {
        $tmp    = false;
        $keyLen = strlen($key);
        if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
            $tmp = substr($key, 6);
        } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
            $tmp = substr($key, 5);
        }
 
        if ($tmp) {
            // make sure first char is lowercase
            $tmp[0] = strtolower($tmp[0]);
            $this->_label_attribs[$tmp] = $val;
            unset($attribs[$key]);
        }
    }
 
    $this->_labelPlacement = 'append';
    foreach ($this->_label_attribs as $key => $val) {
        switch (strtolower($key)) {
            case 'placement':
                unset($this->_label_attribs[$key]);
                $val = strtolower($val);
                if (in_array($val, array('prepend', 'append'))) {
                    $this->_labelPlacement = $val;
                }
                break;
        }
    }
 
    // the radio button values and labels
    $options = (array) $options;
 
    // build the element
    $xhtml = '';
    $list  = array();
 
    // should the name affect an array collection?
    $name = $this->view->escape($name);
    if ($this->_isArray && ('[]' != substr($name, -2))) {
        $name .= '[]';
    }
 
    // ensure value is an array to allow matching multiple times
    $value = (array) $value;
 
    // XHTML or HTML end tag?
    $this->_endTag = ' />';
    if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
        $this->_endTag= '>';
    }
    $this->_id = $id;
    $this->_name = $name;
    $this->_attribs = $attribs;
 
    // add radio buttons to the list.
    require_once 'Zend/Filter/Alnum.php';
    $this->_filter = new Zend_Filter_Alnum();
   
    foreach ($options as $opt_value => $opt_label) {
      if (is_array($opt_label))
      {
        $list[] = $opt_value;
        foreach ($opt_label as $val => $lab) {
            $list[] = $this->_build($val, $lab, $value, $disable, $escape);
        }
      } else {
        $list[] = $this->_build($opt_value, $opt_label, $value, $disable, $escape);
      }
    }
 
    // done!
    $xhtml .= implode($listsep, $list);
    return $xhtml;
   
   
  }
 
  protected function _build($opt_value, $opt_label, $value, $disable, $escape)
  {
    // Should the label be escaped?
    if ($escape) {
        $opt_label = $this->view->escape($opt_label);
    }
 
    // is it disabled?
    $disabled = '';
    if (true === $disable) {
        $disabled = ' disabled="disabled"';
    } elseif (is_array($disable) && in_array($opt_value, $disable)) {
        $disabled = ' disabled="disabled"';
    }
 
    // is it checked?
    $checked = '';
    if (in_array($opt_value, $value)) {
        $checked = ' checked="checked"';
    }
 
    // generate ID
    $optId = $this->_id . '-' . $this->_filter->filter($opt_value);
 
    // Wrap the radios in labels
    $radio = '<label'
            . $this->_htmlAttribs($this->_label_attribs) . ' for="' . $optId . '">'
            . (('prepend' == $this->_labelPlacement) ? $opt_label : '')
            . '<input type="' . $this->_inputType . '"'
            . ' name="' . $this->_name . '"'
            . ' id="' . $optId . '"'
            . ' value="' . $this->view->escape($opt_value) . '"'
            . $checked
            . $disabled
            . $this->_htmlAttribs($this->_attribs)
            . $this->_endTag
            . (('append' == $this->_labelPlacement) ? $opt_label : '')
            . '</label>';
    return $radio;
  }
}

