import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { Router } from '@angular/router';
import jwt_decode, {JwtPayload,JwtDecodeOptions,JwtHeader} from "jwt-decode";
import { TestServiceService } from 'src/app/tweets-service.service';
@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {
  tweets!:Tweet[];
  username!:string;
  showAll = true;
  constructor(private tweetService:TestServiceService, private router: Router) {
    this.tweetService.refreshToken().subscribe(
        res => {
          if(res){
              localStorage.setItem('token', res.jwt);
            
          }
          if(!res){
            console.log('valid');
          }
          

        }
      );
    
      this.getTweets();
      this.getUsername();
   }
   getTweets(){
    this.tweetService.getTweets().subscribe(tweets=>{
      
      this.tweets=tweets},
      err=>{
        if(err instanceof HttpErrorResponse){
          if(err.status === 0){
            //Why err.status == 0???
            //Not authorized users can not access tweets
            console.log(err.message);
            this.router.navigate(['/signup']);
          }
        }
      });
   }
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
          this.router.navigate(['/login']);
        }
      }
    );
  }
  myProfile(){
    
  }
  getUsername(this: any){
    try{
      const token = localStorage.getItem('token');
      const payload: any = jwt_decode(token!);
      this.username = payload.data['username'];
    } catch(error){
      console.log(error);
      this.router.navigate(['/login']);
    }

  }
  ngOnInit(): void {
    
}
addTweet(text:string){
  const newTweet:Tweet = {
    username: this.username,
    tweet: text
  }
  this.tweetService.postTweet(newTweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}
deleteTweet(tweet:Tweet){
  this.tweetService.removeTweet(tweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}
myTweets(){
  //console.log('hey');
  let myTweets: Tweet[]=[];
  this.tweets.forEach(tweet => {
    if(this.username === tweet.username){
      //console.log(tweet);
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