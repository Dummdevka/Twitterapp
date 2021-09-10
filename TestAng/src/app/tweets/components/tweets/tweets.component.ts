import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { Router } from '@angular/router';
import { TestServiceService } from 'src/app/tweets-service.service';
@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {
  tweets!:Tweet[];
  
  constructor(private tweetService:TestServiceService, private router: Router) {
    this.tweetService.getTweets().subscribe(tweets=>{
      if(!tweets){
        this.tweetService.refreshToken().subscribe(
          res=>{
            localStorage.setItem('token', res.jwt);
          }
        );

      }
      console.log("Got the tweets");
      this.tweets=tweets},
      err=>{
        if(err instanceof HttpErrorResponse){
          if(err.status === 0){
            //Why err.status == 0???
            //Not authorized users can not access tweets
            console.log(err.message);
            //this.router.navigate(['/signup']);
          }
        }
      });
   }

  ngOnInit(): void {
    
}
addTweet(newTweet:Tweet){
  this.tweetService.postTweet(newTweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}
deleteTweet(tweet:Tweet){
  this.tweetService.removeTweet(tweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}

}