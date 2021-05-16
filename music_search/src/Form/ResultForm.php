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
            $arr_discogs = $_SESSION['d1']['results'];

            $form['test'] = array(                
                //'#suffix' => '<pre>' . print_r($arr, true) . '</pre></br>' . '<pre>' . print_r($arr_discogs, true) . '</pre>',
                //'#suffix' => '<pre>' . print_r($arr_discogs, true) . '</pre>',
                '#options' => array_merge($this->artist_option($arr), $this->discogs_artist_option($arr_discogs)),
                //'#options' => array_merge($this->artist_option($arr), $this->discogs_artist_option($arr_discogs)),
                '#type' => 'checkboxes',
            );

        } elseif ($_SESSION['s2'] == 'album'){
            $arr = $_SESSION['s1']['albums']['items'];
            $arr_discogs = $_SESSION['d1']['results'];

            $form['test'] = array(
                '#type' => 'checkboxes',
             //   '#suffix' => '<pre>' . print_r($arr, true) . '</pre>',
                '#options' => array_merge($this->album_option($arr), $this->discogs_album_option($arr_discogs)),
            );

        } elseif ($_SESSION['s2'] == 'track'){
            $arr = $_SESSION['s1']['tracks']['items'];

            $form['test'] = array(
                '#type' => 'radios',
              //  '#suffix' => '<pre>' . print_r($arr, true) . '</pre>',
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
        //\Drupal::messenger()->addMessage(t('Jibbi'));

        $_SESSION['con'] = $form_state->getValue('test');

        $url = \Drupal\Core\Url::fromRoute('music_search.cr_content');

        $form_state->setRedirectUrl($url);
    }

    private function artist_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key . ' sp'] = '<img src='. '"' . $value['images'][0]['url'] . '" width="100"  align="center">' .'<strong>'. $value['name'].' (Spotify) </strong>'.'<hr>';
        }

        return $option;
    }

    private function discogs_artist_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key . ' dc'] = '<img src='. '"' . $value['cover_image'] . '" width="100" align="center">' .'<strong>'.$value['title'].' (Discogs) </strong>'.'<hr>';
        }

        return $option;
    }

    private function album_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key . ' sp'] = '<img src='. '"' . $value['images'][0]['url'] . '" width="100" align= "center">'.'<strong>'.$value['name'].' (Spotify) </strong>'.t('  By Artist: '). t($value['artists'][0]['name']).'<hr>';
        }

        return $option;

    }

    private function discogs_album_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key. ' dc'] = '<img src='. '"' . $value['cover_image'] . '" width="100" align="center">'.'<strong>'. $value['title'].' (Discogs) </strong>'.'<hr>';
        }

        return $option;

    }


    private function track_option($arr){
        $option = [];
        foreach($arr as $key => $value){
            $option[$key] = '<img src='. '"' . $value['album']['images'][0]['url'] . '" width="100" align="center">'.'<strong>'.$value['name'].' (Spotify) </strong>'.t('  By Artist: '). t($value['artists'][0]['name']).'<hr>';
        }

        return $option;
    }

 }