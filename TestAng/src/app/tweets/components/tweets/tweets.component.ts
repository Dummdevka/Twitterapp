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
  valid: boolean = false;
  
  constructor(private tweetService:TestServiceService, private router: Router) {
    this.tweetService.refreshToken().subscribe(
        res => {
          if(res){
            console.log(res.jwt);
            localStorage.setItem('token', res.jwt);
          }
          if(!res){
            console.log('valid');
          }
          

        }
      );
    this.tweetService.getTweets().subscribe(tweets=>{
      //console.log(this.tweets);
      console.log(tweets);
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

  ngOnInit(): void {
    // 
    // this.tweetService.checkAllow().subscribe(res=>
    //   {
    //     //console.log(res);
    //     //If the token has expired then refresh it
    //     if(!res){
    //       this.tweetService.refreshToken().subscribe(
    //         result=>{
    //           //Setting the token in local Storage
    //           localStorage.setItem('token', result.jwt);
    //           console.log(result.expire_at);
              
    //         }
    //       );
    //     }
    //     if(res){
    //       console.log(res);
    //     }
    //   });
}
addTweet(newTweet:Tweet){
  this.tweetService.postTweet(newTweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}
deleteTweet(tweet:Tweet){
  this.tweetService.removeTweet(tweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}


}