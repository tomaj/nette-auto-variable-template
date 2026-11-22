# Security Policy

## Supported Versions

Security fixes are provided for the lines below.

| Version | Supported |
| ------- | --------- |
| 4.x     | In development on `master` (not released yet) |
| 3.x     | Yes: bug fixes and security fixes |
| < 3.0   | No |

3.x is the current stable line. When 4.0.0 is released, 4.x becomes the stable line and 3.x gets security fixes only, for six months after the 4.0.0 release date; there is no 3.x maintenance branch, so such a fix is released from a branch created from the last 3.x tag. Versions older than 3.0 do not receive security updates.

## Reporting a Vulnerability

Do not open a public GitHub issue for a security vulnerability.

The preferred way to report a vulnerability is [GitHub private vulnerability reporting](https://github.com/tomaj/nette-auto-variable-template/security/advisories/new).

If you cannot use private vulnerability reporting, email the details to **tomasmajer@gmail.com**.

Please include:

- A description of the vulnerability
- Steps to reproduce, or a proof of concept
- The nette-auto-variable-template, nette/application and Latte versions
- The potential impact
- A suggested fix, if you have one

### Response

You will receive an acknowledgement within 14 days.

### Coordinated disclosure

We follow coordinated disclosure. Please give us time to prepare a fix before discussing the issue in public.

When a fix is ready, we publish a [GitHub Security Advisory](https://github.com/tomaj/nette-auto-variable-template/security/advisories) and record the security fix in the [CHANGELOG](CHANGELOG.md).

## Security considerations

Every variable registered with `registerVariable()` is assigned to every template the factory creates: presenter templates, component templates, e-mail templates and anything else rendered through it.

- Do not register secrets (API keys, passwords, tokens) or whole configuration arrays that contain them. A template, or a third-party component rendering one, can print any variable it receives.
- Register services only when every template may use them; a template can call any public method of a registered object.
- Latte escapes the variables when they are printed with `{$variable}`. Output with `{$variable|noescape}` is your responsibility.
