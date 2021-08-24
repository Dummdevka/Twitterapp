import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import{faPaperPlane} from '@fortawesome/free-regular-svg-icons'
@Component({
  selector: 'app-add-tweets',
  templateUrl: './add-tweets.component.html',
  styleUrls: ['./add-tweets.component.css']
})
export class AddTweetsComponent implements OnInit {
  text!:string;
  username!:string;
  @Output() onAddTweet = new EventEmitter;
  faPaperPlane = faPaperPlane;
  constructor( ) { }

  ngOnInit(): void {
  }
  AddTweet(){
    //Validation
  if(!this.text && !this.username){
    alert("You forgot something :(");
    return;
  }
  //Creating a new Tweet
  const newTweet:Tweet = {
    username: this.username,
    tweet: this.text
  }
  //Call the service
  this.onAddTweet.emit(newTweet);  
  //Clear fields
  this.text = '';
  this.username = '';
  }
}
