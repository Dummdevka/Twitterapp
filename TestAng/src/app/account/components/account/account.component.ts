import { Component, OnInit } from '@angular/core';
import { faCheckCircle } from '@fortawesome/free-regular-svg-icons';
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons';
import { AccountService } from '../../servers/account.service';
import { User } from 'src/app/User';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.css']
})
export class AccountComponent implements OnInit {
  username!: string;
  email!: string;
  newUsername: boolean = false;
  newEmail: boolean = false;
  faPencil = faPencilAlt;
  faCheckCircle = faCheckCircle;
  constructor(private accountService: AccountService) { }

  ngOnInit(): void {
    this.getData();
  }
  getData(){
    this.accountService.getData().subscribe(
      res =>{
        this.username = res.username!;
        this.email = res.email!;
        console.log(this.username);
      },
      err=>{
        console.log(err);
      }
    )
  }
  changeUsername(){
    //Show input
    this.newUsername = true;
  }
  sendUsername(event: any){
    let input: string = event.target.user.value;
    if(input.length === 0){
      //Hide input
      this.newUsername = false;
    }
    if(input.length > 5&& input.length<25){

      //Post data
      const changedUsername = {
        username : input
      };

      //Send new username
      this.accountService.changeUsername(changedUsername).subscribe(
        res=>{

          //If false is returned
          if(!res){
            console.log("Some error((");
            this.newUsername = false;
          }

          //Refreshing the username on the page
          this.getData();
          this.newUsername = false;
        },
        err=>{
          console.log(err.message);
          if(err instanceof HttpErrorResponse){
            if(err.status === 404){
              alert("Invalid username :(");
              
            }
          }
        }
      )
    } else{
      alert("Invalid username :(");
      this.newUsername = false;
    }
    
  }
  changeEmail(){
    //Show input
    this.newEmail = true;
  }
  sendEmail(){
    //Hide input
    this.newEmail = false;
  }
}
