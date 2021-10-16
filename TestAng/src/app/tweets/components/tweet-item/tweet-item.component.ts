import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
@Component({
  selector: 'app-tweet-item',
  templateUrl: './tweet-item.component.html',
  styleUrls: ['./tweet-item.component.less']
})
export class TweetItemComponent implements OnInit {
  @Output() onDeleteTweet = new EventEmitter;
  @Input() tweet!:Tweet;
  @Input() username!: string;
  faTrash = faTrash;
  constructor() { }

  ngOnInit(): void {
  }
  DeleteTweet(tweet:Tweet){
    this.onDeleteTweet.emit(tweet);
  }
}
