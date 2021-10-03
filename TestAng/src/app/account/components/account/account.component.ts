import { Component, OnInit } from '@angular/core';
import { faCheckCircle } from '@fortawesome/free-regular-svg-icons';
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons';
import { AccountService } from '../../servers/account.service';
import { User } from 'src/app/User';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { TestServiceService } from 'src/app/tweets-service.service';
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
  newPass = false;
  faPencil = faPencilAlt;
  faCheckCircle = faCheckCircle;
  constructor(private accountService: AccountService, private tweetService: TestServiceService ,private router: Router) {
    this.checkAllow();
   }

  ngOnInit(): void {
    //this.checkAllow();
    this.getData();
  }
  checkAllow(){
    // this.tweetService.refreshToken().subscribe(
    //   res => {
    //     if(res){
    //       //Storing refreshed token
    //         try{
    //           localStorage.setItem('token', res.jwt);
    //           console.log('refreshed');
    //         } catch(error){
    //           console.log(error);
    //         }
    //     }
    //     if(!res){
    //       //In case the token is valid          
    //       console.log('valid');
    //     }
    //   },
    //   err => {
    //     //If there are any errors - log out
    //     if(err instanceof HttpErrorResponse){
    //       if(err.status === 404){
    //         console.log(err.message);
    //       }
    //       if(err.status === 403){
    //         console.log('No refresh token');
    //       }
    //       this.router.navigate(['/tweets']);
    //     }
    //   })
    }
  getData(){
    //this.checkAllow();
    this.accountService.getData().subscribe(
      res =>{
        this.username = res.username!;
        this.email = res.email!;
        console.log(this.username);
      },
      err=>{
        console.log(err);
        //Log out
        this.router.navigate(['/tweets']);
      }
    )
  }
  changeUsername(){
    //Show input
    this.newUsername = true;
  }
  sendUsername(event: any){
    //this.checkAllow();
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
            alert("Username already exists!");
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
  showChangePass(){
    this.newPass = true;
  }
  changePass(event: any){
    this.checkAllow();
    //Get data from inputs
    let oldPass: string = event.target.old.value;
    let newPass: string = event.target.new.value;
    //Validate it
    if(oldPass.length !== 0 && newPass.length>6){
      const passwords = {
        old : oldPass,
        new : newPass 
      };
          //Request
      this.accountService.changePass(passwords).subscribe(
        res=>{
          if(!res){
            alert("Some errors");
          }
          if(res){
            alert("Success!");
          }
        },
          err=>{
            //console.log(err.error);
            if(err instanceof HttpErrorResponse){
              if(err.status === 405){
                alert(err.error);
                
              }
            }
          }
      )
      //Clear the form
      event.target.old.value = '';
      event.target.new.value = '';
    } else{
      alert("Enter the data, please");
    }
  }
  deleteUser(){
    if(confirm("Are you sure?")){
      this.checkAllow();
      this.accountService.deleteUser().subscribe(
        res=>{
          //Clear the access token
          localStorage.clear;
          //Go to the main page to log out
          this.router.navigate(['/tweets']);
        },
        err=>{
          if(err instanceof HttpErrorResponse){
            alert(err.error);
          }
        }
      )
    }
  }
}
