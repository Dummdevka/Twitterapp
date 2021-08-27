import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, ValidationErrors, Validators } from '@angular/forms';
@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css']
})
export class SignUpComponent implements OnInit {


  signupForm!: FormGroup;

  constructor( private fb: FormBuilder) { 

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
        Validators.required,
        Validators.minLength(6)
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
}
