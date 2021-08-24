import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { AppComponent } from './app.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { HeaderComponent } from './tweets/components/header/header.component';
import { AddTweetsComponent } from './tweets/components/add-tweets/add-tweets.component';
import { TweetsComponent } from './tweets/components/tweets/tweets.component';
import { TweetItemComponent } from './tweets/components/tweet-item/tweet-item.component';
import { SignUpComponent } from './auth/components/sign-up/sign-up.component';
import { LogInComponent } from './auth/components/log-in/log-in.component';
import { ButtonComponent } from './auth/components/button/button.component';
@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    AddTweetsComponent,
    TweetsComponent,
    TweetItemComponent,
    SignUpComponent,
    LogInComponent,
    ButtonComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    FontAwesomeModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
