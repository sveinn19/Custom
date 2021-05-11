<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
* Form for searching music.
 */

 class MusicSearch extends FormBase{

    public function getFormId(){
        return 'music_search_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $form['sstring'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Searchbar"),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        return [
            '#markup' => $this->t($form_state->getValue('sstring')),
          ];

    }



 }