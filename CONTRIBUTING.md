# Contributing to ClassTest Studio

Thank you for considering contributing to ClassTest Studio!

## Code Standards

### PHP Standards
- Follow PSR-4 autoloading
- Follow PSR-1 and PSR-2 coding standards
- Use type hints where appropriate
- Write self-documenting code
- Avoid unnecessary comments

### Code Quality Guidelines
1. **Clean Code**: Write readable, maintainable code
2. **Separation of Concerns**: Keep controllers thin, models focused
3. **DRY Principle**: Don't repeat yourself
4. **KISS Principle**: Keep it simple
5. **Security First**: Always sanitize inputs and use prepared statements

### File Structure
- Controllers: Handle HTTP requests, delegate to models
- Models: Database operations and business logic
- Views: Presentation layer only
- Helpers: Utility functions

### Naming Conventions
- Classes: PascalCase (e.g., `AssessmentController`)
- Methods: camelCase (e.g., `createAssessment()`)
- Variables: camelCase (e.g., `$assessmentId`)
- Database tables: snake_case (e.g., `exam_types`)
- Files: Match class names

## Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Test thoroughly
5. Commit with clear messages (`git commit -m 'Add amazing feature'`)
6. Push to your fork (`git push origin feature/AmazingFeature`)
7. Open a Pull Request

## Testing

Before submitting a PR:
1. Test all CRUD operations
2. Test with both SQLite and MySQL
3. Verify security measures
4. Check for SQL injection vulnerabilities
5. Test with different user roles
6. Ensure no PHP errors or warnings

## Code Review Checklist

- [ ] Code follows project standards
- [ ] No security vulnerabilities
- [ ] SQL queries use prepared statements
- [ ] User inputs are sanitized
- [ ] Error handling is appropriate
- [ ] Code is well-structured
- [ ] No debugging code left behind
- [ ] Documentation is updated

## Feature Requests

Open an issue with:
- Clear description
- Use case
- Expected behavior
- Any technical considerations

## Bug Reports

Include:
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots if applicable
- PHP version
- Database type

## Questions?

Open an issue for discussion!
