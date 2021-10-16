import { Directive } from '@angular/core';
import { AbstractControl, Validator, NG_VALIDATORS } from '@angular/forms';
@Directive({
  selector: '[appUsernameValidator]',
  providers: [{
    provide: NG_VALIDATORS,
    useExisting: UsernameValidatorDirective,
    multi: true
  }]
})
export class UsernameValidatorDirective implements Validator {
  validate(control: AbstractControl) : {[key: string]: any} | null {
    if(control.value && control.value.length != 10 ) {
      return { 'invalidUsername' : true };
    }
    return null;
  }
  constructor() { }

}
