import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css']
})
export class SignUpComponent implements OnInit {

  username!:string;
  email!:string;
  pass!: string;


  constructor() { 

  }
  CheckUsername(){
    if(this.username.length<7){
      console.log('too short');
    }
  }
  CheckEmail(){
    if(this.username.length<7){
      console.log('too short');
    }
  }
  CheckPass(){
    if(this.username.length<7){
      console.log('too short');
    }
  }
  ngOnInit(): void {
  }

}
