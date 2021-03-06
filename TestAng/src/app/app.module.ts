import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { AppComponent } from './app.component';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';
import { HeaderComponent } from './tweets/components/header/header.component';
import { AddTweetsComponent } from './tweets/components/add-tweets/add-tweets.component';
import { TweetsComponent } from './tweets/components/tweets/tweets.component';
import { TweetItemComponent } from './tweets/components/tweet-item/tweet-item.component';
import { SignUpComponent } from './auth/components/sign-up/sign-up.component';
import { LogInComponent } from './auth/components/log-in/log-in.component';
import { ButtonComponent } from './auth/components/button/button.component';
import { AppRoutingModule } from './app-routing.module';
import { Routes, RouterModule } from '@angular/router';
import { AuthService } from './auth/auth.service';
import { AuthGuard } from './auth.guard';
import { AccountComponent } from './account/components/account/account.component';
import { TweetsButtonComponent } from './tweets/components/my_tweets_button/tweets-button/tweets-button.component';
import { TweetsInterceptorInterceptor } from './tweets/tweets-interceptor.interceptor';
import { UsernameValidatorDirective } from './auth/components/sign-up/validators/username-validator.directive';

const routes: Routes = [
  {path: 'tweets', 
  component: TweetsComponent,
  
},
  {path: 'signup',
   component: SignUpComponent},
  {path: 'account',
    component: AccountComponent,
  },
  {path: '', redirectTo: '/signup', pathMatch: 'full'},
  {path: 'login', component: LogInComponent}
];

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    AddTweetsComponent,
    TweetsComponent,
    TweetItemComponent,
    SignUpComponent,
    LogInComponent,
    ButtonComponent,
    AccountComponent,
    TweetsButtonComponent,
    UsernameValidatorDirective,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    FontAwesomeModule,
    AppRoutingModule,
    RouterModule.forRoot(
      routes,
      { enableTracing: true } // <-- debugging purposes only
    )
  ],
  providers: [AuthService, AuthGuard,
  {
    provide: HTTP_INTERCEPTORS,
    useClass: TweetsInterceptorInterceptor,
    multi: true
  }],
  bootstrap: [AppComponent]
})
export class AppModule { }
