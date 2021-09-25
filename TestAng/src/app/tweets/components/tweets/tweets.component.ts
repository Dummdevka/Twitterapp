import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { Router } from '@angular/router';
import jwt_decode, {JwtPayload,JwtDecodeOptions,JwtHeader} from "jwt-decode";
import { TestServiceService } from 'src/app/tweets-service.service';
import { TweetsInterceptorInterceptor } from '../../tweets-interceptor.interceptor';
@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {
  tweets!:Tweet[];
  username!:string;
  showAll = true;
  allow!: boolean;
  constructor(public tweetService:TestServiceService, private router: Router) {
    
      this.checkAllow();
      this.getTweets();
      this.getUsername();
   }
   checkAllow(){
    this.tweetService.refreshToken().subscribe(
      res => {
        if(res){
          //Storing refreshed token
            try{
              localStorage.setItem('token', res.jwt);
              // console.log('refreshed');
              console.log('refreshed');
              
            } catch(error){
              console.log(error);

              //return false;
            }
            
        }
        if(!res){
          //In case the token is valid
          //this.allow = true;
          
          console.log('valid');
        }
      },
      err => {
        //If there are any errors - log out
        if(err instanceof HttpErrorResponse){
          if(err.status === 404){
            console.log(err.message);
          }
          if(err.status === 403){
            console.log('No refresh token');
          }
          this.onLogOut();

          //return process.exit(0);

          //this.allow = false;
          //return;
        }
      }
    );
    //return false;
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
            console.log(err);
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
  ngOnInit(): void {
    
}
addTweet(text:string){
  //Refresh the token before sending the tweet
  this.checkAllow()
  const newTweet:Tweet = {
    username: this.username,
    tweet: text
  }
  this.tweetService.postTweet(newTweet).subscribe((tweets:Tweet[])=>{
    this.tweets = tweets;
    //window.location.reload();
  });
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
