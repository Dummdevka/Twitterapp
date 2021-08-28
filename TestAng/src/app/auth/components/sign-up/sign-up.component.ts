import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { User } from 'src/app/User';
import { AuthService } from '../../auth.service';
import { Observable } from 'rxjs';
import { FormBuilder, FormControl, FormGroup, ValidationErrors, Validators } from '@angular/forms';
@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css']
})
export class SignUpComponent implements OnInit {

  @Output() signUpFunc = new EventEmitter;
  signupForm!: FormGroup;

  constructor( private fb: FormBuilder, private authService: AuthService) { 

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

  onSignUp(username:string, email:string, pass:string){

    if(this.signupForm.status === 'VALID'){
      const user: User = {
        username: username,
        email: email,
        pass:pass
      }
      this.authService.addUser(user).subscribe((response)=>{
        console.log(response);
      },
      (error)=>{
        console.log(error);
      });
    } else{
      alert("You can't submit an invalid form!");
    }
    

  }
}
