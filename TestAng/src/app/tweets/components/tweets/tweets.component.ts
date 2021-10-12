import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { Router } from '@angular/router';
import jwt_decode, {JwtPayload,JwtDecodeOptions,JwtHeader} from "jwt-decode";
import { TestServiceService } from 'src/app/tweets-service.service';
import { TweetsInterceptorInterceptor } from '../../tweets-interceptor.interceptor';
import { forkJoin } from 'rxjs';
@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {
  tweets!:Tweet[];
  error = '';
  username!:string;
  showAll = true;
  uploadedImg!: File;
  constructor(public tweetService:TestServiceService, private router: Router) {
    
      
   }
   async checkAllow(){
    try{
      let refresh = await this.tweetService.refreshToken();
    if(refresh){
      localStorage.setItem('token', refresh.jwt);
      console.log(refresh.jwt);
    }
    if(!refresh){
      console.log(refresh);
      console.log('valid');
    }
    
    } catch(error){
      if(error instanceof HttpErrorResponse){
        console.log(error.error);
      }
      this.onLogOut();
    }
   }
   getTweets(){
      this.checkAllow();
      this.tweetService.getTweets().subscribe(
        tweets=>{
        if(!tweets){
  
          //Reload the page again to show the tweets
          
        }
        this.tweets=tweets
      },
  
        err=>{
          if(err instanceof HttpErrorResponse){
            if(err.status === 0){
              
              
            }
            this.error = "Tweets could not be fetched :(";
            //this.onLogOut();
          }
        });
      } 
      //}
      
  
   onLogOut(){
    //Clean localStorage
    localStorage.clear();
    //Clean cookie
    this.tweetService.clearRefresh().subscribe(
      res=>{
        if(!res){
          console.log('You should change your password');
          this.router.navigate(['/login']);
        } 
        else{
          console.log(res);
          this.router.navigate(['/login']);
        }
      }
    );
  }
  
  getUsername(this: any){
    try{
      const token = localStorage.getItem('token');
      const payload: any = jwt_decode(token!);
      this.username = payload.data['username'];
    } catch(error){
      console.log(error);
    }

  }
 async ngOnInit() {
      await this.checkAllow();
      this.getTweets();
      this.getUsername();
      
}
addTweet(tweet:Tweet){
  //Refresh the token before sending the tweet
  this.checkAllow();
  console.log(tweet);
  const newTweet:Tweet = {
    username: this.username,
    tweet: tweet.tweet,
    //image: tweet.image
  }
  //If there is a picture - send it as well
  
  let tweetData = new FormData;
  tweetData.append('username', this.username);
  tweetData.append('tweet', tweet.tweet);

  //If there are any pictures attached
  if(tweet.image !== null){
    tweetData.append('tweet-attachments', tweet.image);
  }
  //Post tweet
  this.tweetService.postTweet(tweetData).subscribe((tweets:Tweet[])=>{
    this.tweets = tweets;
  },
  err=>{
    if(err instanceof HttpErrorResponse){
      if(err.status === 415){
        alert(err.error);
      }
      if(err.status === 500){
        alert(err.error);
      }
      //Alert error that tweet could not be posted
      //this.error = "Tweet could not be posted";
      alert(err.message);
    }
  }
  );

  
}

deleteTweet(tweet: Tweet){
  
  this.checkAllow();
  
  this.tweetService.removeTweet(tweet).subscribe((tweets:Tweet[])=>{
    this.tweets = tweets;
  });

  //Refresh the token before removing the tweet
  

}


myTweets(){
  let myTweets: Tweet[]=[];
  this.tweets.forEach(tweet => {
    if(this.username === tweet.username){
      myTweets.push(tweet);
    }

  })
  this.tweets = myTweets;
  this.showAll = false;
  ;
}
allTweets(){
  this.getTweets();
  this.showAll=true;
}
}
