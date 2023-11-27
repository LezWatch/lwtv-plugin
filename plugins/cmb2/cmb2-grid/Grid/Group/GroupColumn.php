<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LWTV\Cmb2Grid\Grid\Group;

if ( ! class_exists( 'LWTV\Cmb2Grid\Grid\Group\GroupColumn' ) ) {

	/**
	 * Description of GroupColumn.
	 *
	 * @author Pablo
	 */
	class GroupColumn extends \LWTV\Cmb2Grid\Grid\Column {

		protected $parentFieldId;

		public function setColumnClassCmb2() {
			$columnClass = $this->getColumnClass();
			$field		 = $this->getField();
			$fieldID	 = $this->getFieldId();

			//\LWTV\Cmb2Grid\Cmb2\Utils::initializeFieldArg( $field->args['fields'][$fieldID], 'before_row' );
			//\LWTV\Cmb2Grid\Cmb2\Utils::initializeFieldArg( $field->args['fields'][$fieldID], 'after_row' );
			\LWTV\Cmb2Grid\Cmb2\Utils::appendGroupFieldArg( $field, $fieldID, 'before_row',  "<div class=\"{$columnClass}\">" );
			\LWTV\Cmb2Grid\Cmb2\Utils::appendGroupFieldArg( $field, $fieldID, 'after_row',  "</div>" );
		}

		public function __construct( $field, \LWTV\Cmb2Grid\Grid\Cmb2Grid $grid ) {

			if ( is_array( $field ) && isset( $field['class'] ) ) {
				$this->setColumnClass( $field['class'] );
				$field = $field[0];
			}

			$this->setParentFieldId( $field[0] );
			$this->setFieldId( $field[1] );
			$field = cmb2_get_field( $grid->getCmb2Obj(), $this->getParentFieldId() );
			$this->setField( $field );

			//parent::__construct( $field, $grid );

			/* $this->setGrid( $grid );
			  if ( is_string( $field ) ) {
			  $this->setFieldId( $field );
			  } elseif ( is_array( $field ) ) {
			  $this->setFieldId( $field[0] );
			  }
			  $fieldId = $this->getFieldId();


			  $field = cmb2_get_field( $grid->getCmb2Obj(), $fieldId );

			  $this->setField( $field );

			  if ( is_array( $field ) ) {
			  if ( isset( $field['class'] ) ) {
			  $this->setColumnClass( $field['class'] );
			  }
			  } */
		}

		function getParentFieldId() {
			return $this->parentFieldId;
		}

		function setParentFieldId( $parentFieldId ) {
			$this->parentFieldId = $parentFieldId;
		}
	}
}
