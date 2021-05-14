<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* Form for searching music.
 */

 class ResultForm extends FormBase{

    public function getFormId(){
        return 'result_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state){
        // TYPES
        // 'album' => t('Album'),
        // 'artist' => t('Artists'),
        // 'track' => t('Songs'),

        $form['back'] = array(
            '#type' => 'button',
            '#value' => t('Search more music.'),
            '#attributes' => array(
              'onclick' => 'window.history.back();return false;',
            ),
          );

        if ($_SESSION['s2'] == 'artist'){
            $arr = $_SESSION['s1']['artists']['items'];

            $form['test'] = array(                
                '#suffix' => '<pre>' . print_r($arr, true) . '</pre>',
                '#options' => $this->artist_option($arr),
                '#type' => 'radios',
            );

        } elseif ($_SESSION['s2'] == 'album'){
            $arr = $_SESSION['s1']['albums']['items'];

            $form['test'] = array(
                '#type' => 'radios',
                '#suffix' => '<pre>' . print_r($arr, true) . '</pre>',
                '#options' => $this->album_option($arr),
            );

        } elseif ($_SESSION['s2'] == 'track'){
            $arr = $_SESSION['s1']['tracks']['items'];

            $form['test'] = array(
                '#type' => 'radios',
                '#suffix' => '<pre>' . print_r($arr, true) . '</pre>',
                '#options' => $this->track_option($arr),
            );
        }

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('Jibbi'));

        $_SESSION['con'] = $form_state->getValue('test');

        $url = \Drupal\Core\Url::fromRoute('music_search.cr_content');

        $form_state->setRedirectUrl($url);
    }

    private function artist_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key] = '<h2">'.$value['name'].'</h2>'. '<img src='. '"' . $value['images'][0]['url'] . '" width="200">';
        }

        return $option;
    }

    private function album_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key] = '<h2">'.$value['name'].'</h2>'. '<h2> Artist: '. $value['artists'][0]['name'] .'</h2>'. '<img src='. '"' . $value['images'][0]['url'] . '" width="200">';
        }

        return $option;

    }

    private function track_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key] = '<h2">'.$value['name'].'</h2>'. '<h2> Artist: '. $value['artists'][0]['name'] .'</h2>'. '<img src='. '"' . $value['album']['images'][0]['url'] . '" width="200">';
        }

        return $option;
    }

 }