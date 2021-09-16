import { Component, Input, OnInit } from '@angular/core';
import { Output, EventEmitter } from '@angular/core';
@Component({
  selector: 'app-tweets-button',
  templateUrl: './tweets-button.component.html',
  styleUrls: ['./tweets-button.component.css']
})
export class TweetsButtonComponent implements OnInit {
  @Output() onShowMyTweets = new EventEmitter;
  @Input() text!:string;
  constructor() { }

  ngOnInit(): void {
  }
  showMyTweets(){
    //If tweet.username = this.username -> show
    this.onShowMyTweets.emit();
  }
}
