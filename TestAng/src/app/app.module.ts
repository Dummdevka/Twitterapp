import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { AppComponent } from './app.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { HeaderComponent } from './components/header/header.component';
import { AddTweetsComponent } from './components/add-tweets/add-tweets.component';
import { TweetsComponent } from './components/tweets/tweets.component';
import { TweetItemComponent } from './components/tweet-item/tweet-item.component';
@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    AddTweetsComponent,
    TweetsComponent,
    TweetItemComponent
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
