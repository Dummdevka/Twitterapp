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
  constructor( ) {
    
   }

  ngOnInit(): void {

  }
  AddTweet(){
    //Validation
  if(!this.text){
    alert("You forgot something :(");
    return;
  }
  //Creating a new Tweet
  //Call the service
  this.onAddTweet.emit(this.text);  
  //Clear fields
  this.text = '';
  }
  
}

