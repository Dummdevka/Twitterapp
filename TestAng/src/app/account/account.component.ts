import { Component, OnInit } from '@angular/core';
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons';


@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.css']
})
export class AccountComponent implements OnInit {
  faPencil = faPencilAlt;
  constructor() { }

  ngOnInit(): void {
  }
  changeUsername(){

  }
  changeEmail(){
    
  }
}
