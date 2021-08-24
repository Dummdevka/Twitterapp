import { Component, OnInit } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { TestServiceService } from 'src/app/test-service.service';
@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {
  tweets!:Tweet[];
  
  constructor(private tweetService:TestServiceService) { }

  ngOnInit(): void {
    this.tweetService.getTweets().subscribe((tweets)=>{this.tweets=tweets});
}
addTweet(newTweet:Tweet){
  this.tweetService.postTweet(newTweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}
deleteTweet(tweet:Tweet){
  this.tweetService.removeTweet(tweet).subscribe((tweets:Tweet[])=>{this.tweets = tweets});
}

}