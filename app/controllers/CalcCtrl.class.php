<?php
// W skrypcie definicji kontrolera nie trzeba dołączać już niczego.
// Kontroler wskazuje tylko za pomocą 'use' te klasy z których jawnie korzysta
// (gdy korzysta niejawnie to nie musi - np używa obiektu zwracanego przez funkcję)

// Zarejestrowany autoloader klas załaduje odpowiedni plik automatycznie w momencie, gdy skrypt będzie go chciał użyć.
// Jeśli nie wskaże się klasy za pomocą 'use', to PHP będzie zakładać, iż klasa znajduje się w bieżącej
// przestrzeni nazw - tutaj jest to przestrzeń 'app\controllers'.

// Przypominam, że tu również są dostępne globalne funkcje pomocnicze - o to nam właściwie chodziło

namespace app\controllers;

//zamieniamy zatem 'require' na 'use' wskazując jedynie przestrzeń nazw, w której znajduje się klasa
use app\forms\CalcForm;
use app\transfer\CalcResult;

/** Kontroler kalkulatora
 * @author Przemysław Kudłacik
 *
 */
class CalcCtrl
{

    private $form;   //dane formularza (do obliczeń i dla widoku)
    private $result; //inne dane dla widoku

    /**
     * Konstruktor - inicjalizacja właściwości
     */
    public function __construct()
    {
        //stworzenie potrzebnych obiektów
        $this->form = new CalcForm();
        $this->result = new CalcResult();
    }

    /**
     * Pobranie wartości, walidacja, obliczenie i wyświetlenie
     */
    public function action_calcCompute()
    {

        $this->getParams();

        if ($this->validate()) {

            $this->form->kwota = floatval($this->form->kwota);
            $this->form->lata = intval($this->form->lata);
            $this->form->procent = floatval($this->form->procent);
            getMessages()->addInfo('Parametry poprawne.');

            $this->result->rata = $this->form->kwota / ($this->form->lata * 12);
            $this->result->result = $this->result->rata + ($this->result->rata * ($this->form->procent / 100));
            $this->generateView();
        }

    }

    public function action_calcShow(){
        getMessages()->addInfo('Witaj w kalkulatorze');
        $this->generateView();
    }
    /**
     * Pobranie parametrów
     */
    public function getParams()
    {
        $this->form->kwota = getFromRequest('kwota');
        $this->form->lata = getFromRequest('lata');
        $this->form->procent = getFromRequest('procent');
    }

    /**
     * Walidacja parametrów
     * @return true jeśli brak błedów, false w przeciwnym wypadku
     */
    public function validate()
    {

        if (!(isset($this->form->kwota) && isset($this->form->lata) && isset($this->form->procent))) {

            return false;
        }

        if ($this->form->kwota == "") {
            getMessages()->addError('Nie podano kwoty pożyczki');
        }
        if ($this->form->lata == "") {
            getMessages()->addError('Nie podano lat spłacania pożyczki');
        }
        if ($this->form->procent == "") {
            getMessages()->addError('Nie podano procentu kredytu');
        }

        if (!getMessages()->isError()) {

            if (!is_numeric($this->form->kwota)) {
                getMessages()->addError('Kwota nie jest liczbą całkowitą');
            }

            if (!is_numeric($this->form->lata)) {
                getMessages()->addError('Podany okres czasu nie jest liczbą całkowitą');
            }
            if (!is_numeric($this->form->procent)) {
                getMessages()->addError('Podane oprocentowanie nie jest liczbą całkowitą');
            }
        }
        if (getMessages()->isError()) return false;
        return true;
    }

    /**
     * Wygenerowanie widoku
     */
    public function generateView()
    {

        getSmarty()->assign('user', unserialize($_SESSION['user']));

        getSmarty()->assign('page_title', 'Super kalkulator - role');

        getSmarty()->assign('form', $this->form);
        getSmarty()->assign('res', $this->result);

        getSmarty()->display('CalcView.tpl');
    }
}
