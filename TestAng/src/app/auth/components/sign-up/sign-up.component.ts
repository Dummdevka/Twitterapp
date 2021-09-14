import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { User } from 'src/app/User';
import { AuthService } from '../../auth.service';
import { Observable } from 'rxjs';
import { NavigationExtras, Router } from '@angular/router';
import { FormBuilder, FormControl, FormGroup, ValidationErrors, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css']
})
export class SignUpComponent implements OnInit {
  UserName: string = "Admin";
  Email:string = "Admin123@gmail.com";
  Pwd:string = "Admin123";
  errors: [] = [];
  @Output() signUpFunc = new EventEmitter;
  signupForm!: FormGroup;
  constructor( private fb: FormBuilder, private authService: AuthService, private router:Router) { 

  }
  
  ngOnInit(): void {
    this.signupForm = this.fb.group({
      username: ['', [
        Validators.required,
        Validators.minLength(5),
        Validators.maxLength(25),
      ]],
      email: ['',[
        Validators.required,
        Validators.email
      ]],
      pass: ['',
        [Validators.required,
        Validators.minLength(6)]
      ]
    });
  }

  get username() {
    return this.signupForm.get("username");
  }
  get email() {
    return this.signupForm.get('email');
  }
  get pass() {
    return this.signupForm.get('pass');
  }
  showErrors(errorsArr: any){
    console.log(typeof errorsArr)
  }
  onSignUp(username:string, email:string, pass:string){
    this.errors = [];
    if(this.signupForm.status === 'VALID'){
      const user: User = {
        username: username,
        email: email,
        pass:pass
      }
      this.authService.addUser(user).subscribe(
        (res:User) => {
          console.log(res);
          const navigationExtras: NavigationExtras = {state:{data: `Hi, ${res.username}! Please, log in!` }};
          this.router.navigate(['/login'], navigationExtras);          
        },
        err =>{
          if(err instanceof HttpErrorResponse){
            if(err.status === 422){
              console.log(err.error);
            }
          }
        }
      )
      
    } else{
      alert("You can't submit an invalid form!");
    }
    

  }
}
